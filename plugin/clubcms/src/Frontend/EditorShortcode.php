<?php

declare(strict_types=1);

namespace ClubCMS\Frontend;

use ClubCMS\Admin\CardSubmissionHandler;
use ClubCMS\Domain\Card;
use ClubCMS\Domain\CardStatus;
use ClubCMS\Repository\CardRepositoryInterface;
use ClubCMS\Repository\CategoryRepositoryInterface;
use ClubCMS\Security\EditorAccessGuard;

final class EditorShortcode
{
    /** @var callable(string): void|null */
    private $redirect;

    /** @var callable(): string|null */
    private $requestUri;

    /** @var callable(): string|null */
    private $homeUrl;

    /** @var callable(): void|null */
    private $terminate;

    /** @var callable(): bool|null */
    private $headersSent;

    private string $backToUrl = '';

    private ?string $flashStatus = null;

    private string $templateKey = '';

    private string $duplicateCardId = '';

    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly CardRepositoryInterface $cardRepository,
        private readonly CardSubmissionHandler $submissionHandler,
        private readonly EditorAccessGuard $accessGuard = new EditorAccessGuard(),
        $redirect = null,
        $requestUri = null,
        $homeUrl = null,
        $terminate = null,
        $headersSent = null,
    ) {
        $this->redirect = $redirect;
        $this->requestUri = $requestUri;
        $this->homeUrl = $homeUrl;
        $this->terminate = $terminate;
        $this->headersSent = $headersSent;
    }

    public function register(): void
    {
        add_shortcode('clubcms_editor', [$this, 'render']);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function render(array $attributes = []): string
    {
        if (! $this->accessGuard->canAccess()) {
            return $this->renderAccessDenied();
        }

        $this->backToUrl = $this->extractBackToUrl($attributes);
        $this->templateKey = $this->extractTemplateKey($attributes);
        $this->duplicateCardId = $this->extractDuplicateCardId($attributes);
        $this->handleSubmit();

        $editingCard = $this->getEditingCard();
        $categories = $this->categoryRepository->all();

        ob_start();
        ?>
        <section class="clubcms-editor">
            <header class="clubcms-editor__header">
                <p class="clubcms-editor__kicker">ClubCMS</p>
                <h1 class="clubcms-editor__title">Redaktions-Editor</h1>
                <p class="clubcms-editor__lead">
                    Inhalte, Cards und Themen werden hier zentral gepflegt.
                </p>
            </header>

            <?php echo $this->renderErrorNotice(); ?>
            <?php echo $this->renderStatusNotice(); ?>
            <?php echo $this->renderTemplateChooser($editingCard, $categories); ?>
            <?php echo $this->renderForm($editingCard, $categories); ?>
            <?php echo $this->renderList(); ?>
        </section>
        <?php

        return (string) ob_get_clean();
    }

    private function handleSubmit(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || ($_POST['clubcms_form'] ?? '') !== 'card') {
            return;
        }

        check_admin_referer('clubcms_save_card');

        if (! $this->accessGuard->canAccess()) {
            wp_die('Insufficient permissions.');
        }

        $action = (string) ($_POST['clubcms_action'] ?? 'save');

        if ($action === 'delete') {
            if (! $this->submissionHandler->handleDelete($_POST)) {
                return;
            }

            $this->flashStatus = 'deleted';
            if ($this->redirectOrDefer($this->buildReturnUrl(['deleted' => '1']))) {
                $this->terminate();
            }

            return;
        }

        if (! $this->submissionHandler->handleCard($_POST)) {
            return;
        }

        $this->flashStatus = 'saved';
        if ($this->redirectOrDefer($this->buildReturnUrl(['saved' => '1']))) {
            $this->terminate();
        }
    }

    private function getEditingCard(): ?Card
    {
        $id = (string) ($_GET['edit_card'] ?? '');

        if ($id === '') {
            return null;
        }

        return $this->cardRepository->getById($id);
    }

    /**
     * @param array<int, \ClubCMS\Domain\Category> $categories
     */
    private function renderForm(?Card $editingCard, array $categories): string
    {
        $presetCategoryId = $this->getPresetCategoryId();
        $card = $editingCard ?? new Card('', '', $presetCategoryId, [], publishedAt: null);
        $categoryLabels = [];
        $hasPostValidationError = $this->isSubmittedCardPost() && $this->submissionHandler->getLastError() !== null;

        foreach ($categories as $category) {
            $categoryLabels[$category->id] = $category->label;
        }

        if ($editingCard === null && $this->duplicateCardId !== '') {
            $duplicateSource = $this->cardRepository->getById($this->duplicateCardId);

            if ($duplicateSource !== null) {
                $card = new Card(
                    $duplicateSource->id . '-kopie',
                    $duplicateSource->title . ' Kopie',
                    $duplicateSource->categoryId,
                    $duplicateSource->fields,
                    CardStatus::Draft,
                    $duplicateSource->visibility,
                    $duplicateSource->isStatic,
                    $duplicateSource->position,
                    null
                );
            }
        }

        if ($editingCard === null && $this->templateKey !== '') {
            $card = $this->applyTemplate($card, $this->templateKey);
        }

        $idValue = $hasPostValidationError ? $this->postedString('id', $card->id) : $card->id;
        $titleValue = $hasPostValidationError ? $this->postedString('title', $card->title) : $card->title;
        $categoryIdValue = $hasPostValidationError ? $this->postedString('category_id', $card->categoryId) : $card->categoryId;
        $statusValue = $hasPostValidationError ? $this->postedString('status', $card->status->value) : $card->status->value;
        $visibilityValue = $hasPostValidationError ? $this->postedString('visibility', $card->visibility->value) : $card->visibility->value;
        $positionValue = $hasPostValidationError ? $this->postedString('position', (string) $card->position) : (string) $card->position;
        $publishedAtValue = $hasPostValidationError ? $this->postedString('published_at', $card->publishedAt?->format('Y-m-d H:i:s') ?? '') : ($card->publishedAt?->format('Y-m-d H:i:s') ?? '');
        $fieldsJsonValue = $hasPostValidationError
            ? $this->postedString('fields_json', (string) json_encode($card->fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
            : (string) json_encode($card->fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $isStaticValue = $hasPostValidationError ? $this->postedBoolean('is_static', $card->isStatic) : $card->isStatic;
        $isEditMode = $editingCard !== null;
        $previewCategoryLabel = $categoryLabels[$categoryIdValue] ?? ($categoryIdValue !== '' ? $categoryIdValue : 'Keine Kategorie');
        $previewFields = array_keys($card->fields);
        $previewFieldsSummary = $previewFields === [] ? 'Keine Felder gesetzt' : implode(', ', array_slice($previewFields, 0, 4));

        ob_start();
        ?>
        <article class="clubcms-editor__form">
            <div class="clubcms-editor__form-header">
                <h2><?php echo $isEditMode ? 'Card bearbeiten' : 'Card anlegen'; ?></h2>
                <a class="button button-small" href="<?php echo $this->escapeAttr($this->buildResetUrl()); ?>">Neu starten</a>
            </div>
            <section class="clubcms-editor__preview">
                <h3>Vorschau</h3>
                <p>So wird die Card in der groben Struktur dargestellt.</p>
                <div class="clubcms-editor__preview-card">
                    <p><strong><?php echo $this->escapeHtml($card->title !== '' ? $card->title : 'Ohne Titel'); ?></strong></p>
                    <p>Kategorie: <?php echo $this->escapeHtml($previewCategoryLabel); ?></p>
                    <p>Status: <?php echo $this->escapeHtml($card->status->value); ?></p>
                    <p>Sichtbarkeit: <?php echo $this->escapeHtml($card->visibility->value); ?></p>
                    <p>Position: <?php echo $this->escapeHtml((string) $card->position); ?></p>
                    <p>Felder: <?php echo $this->escapeHtml($previewFieldsSummary); ?></p>
                </div>
            </section>
            <form method="post" action="<?php echo $this->escapeAttr($this->currentUrl()); ?>">
                <?php wp_nonce_field('clubcms_save_card'); ?>
                <input type="hidden" name="clubcms_form" value="card" />
                <input type="hidden" name="clubcms_action" value="save" />
                <input type="hidden" name="original_id" value="<?php echo $this->escapeAttr($idValue); ?>" />
                <input type="hidden" name="back_to" value="<?php echo $this->escapeAttr($this->backToUrl); ?>" />
                <input type="hidden" name="template" value="<?php echo $this->escapeAttr($this->templateKey); ?>" />
                <input type="hidden" name="duplicate_card" value="<?php echo $this->escapeAttr($this->duplicateCardId); ?>" />
                <table class="form-table" role="presentation">
                    <tr>
                        <th><label for="card_id">ID</label></th>
                        <td><input name="id" id="card_id" type="text" class="regular-text" required value="<?php echo $this->escapeAttr($idValue); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="card_title">Titel</label></th>
                        <td><input name="title" id="card_title" type="text" class="regular-text" required value="<?php echo $this->escapeAttr($titleValue); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="category_id">Kategorie</label></th>
                        <td>
                            <select name="category_id" id="category_id">
                                <option value="">Bitte wählen</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $this->escapeAttr($category->id); ?>" <?php echo $category->id === $categoryIdValue ? 'selected' : ''; ?>>
                                        <?php echo $this->escapeHtml($category->label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="status">Status</label></th>
                        <td>
                            <select name="status" id="status">
                                <option value="draft" <?php echo $statusValue === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo $statusValue === 'published' ? 'selected' : ''; ?>>Published</option>
                                <option value="archived" <?php echo $statusValue === 'archived' ? 'selected' : ''; ?>>Archived</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="visibility">Sichtbarkeit</label></th>
                        <td>
                            <select name="visibility" id="visibility">
                                <option value="public" <?php echo $visibilityValue === 'public' ? 'selected' : ''; ?>>Public</option>
                                <option value="members" <?php echo $visibilityValue === 'members' ? 'selected' : ''; ?>>Members</option>
                                <option value="editorial" <?php echo $visibilityValue === 'editorial' ? 'selected' : ''; ?>>Editorial</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="position">Position</label></th>
                        <td><input name="position" id="position" type="number" class="small-text" value="<?php echo $this->escapeAttr($positionValue); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="published_at">Veröffentlicht am</label></th>
                        <td><input name="published_at" id="published_at" type="text" class="regular-text" placeholder="2026-07-13 10:00:00" value="<?php echo $this->escapeAttr($publishedAtValue); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="is_static">Statisch</label></th>
                        <td><label><input name="is_static" id="is_static" type="checkbox" value="1" <?php echo $isStaticValue ? 'checked' : ''; ?>> Statische Card</label></td>
                    </tr>
                    <tr>
                        <th><label for="fields_json">Felder als JSON</label></th>
                        <td><textarea name="fields_json" id="fields_json" class="large-text code" rows="10"><?php echo $this->escapeHtml($fieldsJsonValue); ?></textarea></td>
                    </tr>
                </table>
                <?php submit_button($isEditMode ? 'Card aktualisieren' : 'Card speichern'); ?>
            </form>
        </article>
        <?php

        return (string) ob_get_clean();
    }

    private function renderList(): string
    {
        $items = $this->cardRepository->all();
        $categories = [];

        foreach ($this->categoryRepository->all() as $category) {
            $categories[$category->id] = $category;
        }

        ob_start();
        ?>
        <article class="clubcms-editor__list">
            <h2>Vorhandene Cards</h2>

            <?php if ($items === []): ?>
                <p>Noch keine Cards angelegt.</p>
            <?php else: ?>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titel</th>
                            <th>Kategorie</th>
                            <th>Status</th>
                            <th>Sichtbarkeit</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php $categoryLabel = $categories[$item->categoryId]->label ?? $item->categoryId; ?>
                            <tr>
                                <td><?php echo $this->escapeHtml($item->id); ?></td>
                                <td><?php echo $this->escapeHtml($item->title); ?></td>
                                <td><?php echo $this->escapeHtml($categoryLabel); ?></td>
                                <td><?php echo $this->escapeHtml($item->status->value); ?></td>
                                <td><?php echo $this->escapeHtml($item->visibility->value); ?></td>
                                <td><?php echo $this->renderEditLink($item->id); ?> <?php echo $this->renderDuplicateLink($item->id); ?> <?php echo $this->renderDeleteForm($item->id); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </article>
        <?php

        return (string) ob_get_clean();
    }

    private function renderEditLink(string $id): string
    {
        return '<a class="button button-small" href="' . $this->escapeAttr($this->buildUrl(['edit_card' => $id])) . '">Bearbeiten</a>';
    }

    private function renderDuplicateLink(string $id): string
    {
        return '<a class="button button-small" href="' . $this->escapeAttr($this->buildUrl(['duplicate_card' => $id])) . '">Duplizieren</a>';
    }

    private function renderDeleteForm(string $id): string
    {
        ob_start();
        ?>
        <form method="post" action="<?php echo $this->escapeAttr($this->currentUrl()); ?>" style="display:inline;">
            <?php wp_nonce_field('clubcms_save_card'); ?>
            <input type="hidden" name="clubcms_form" value="card" />
            <input type="hidden" name="clubcms_action" value="delete" />
            <input type="hidden" name="id" value="<?php echo $this->escapeAttr($id); ?>" />
            <input type="hidden" name="back_to" value="<?php echo $this->escapeAttr($this->backToUrl); ?>" />
            <button type="submit" class="button button-small button-link-delete">Löschen</button>
        </form>
        <?php

        return (string) ob_get_clean();
    }

    private function renderStatusNotice(): string
    {
        if (! isset($_GET['saved']) && ! isset($_GET['deleted'])) {
            if ($this->flashStatus === null) {
                return '';
            }

            $message = $this->flashStatus === 'deleted' ? 'Card wurde gelÃ¶scht.' : 'Card wurde gespeichert.';

            return '<div class="notice notice-success is-dismissible"><p>' . $this->escapeHtml($message) . '</p></div>';
        }

        $message = isset($_GET['deleted']) ? 'Card wurde gelöscht.' : 'Card wurde gespeichert.';

        return '<div class="notice notice-success is-dismissible"><p>' . $this->escapeHtml($message) . '</p></div>';
    }

    /**
     * @param array<int, \ClubCMS\Domain\Category> $categories
     */
    private function renderTemplateChooser(?Card $editingCard, array $categories): string
    {
        if ($editingCard !== null) {
            return '';
        }

        $templates = $this->templateDefinitions();
        $currentCategoryId = $this->getPresetCategoryId();
        $baseUrl = $this->currentUrl();

        ob_start();
        ?>
        <article class="clubcms-editor__templates">
            <h2>Vorlage für neue Beiträge</h2>
            <p>Wähle ein Startmuster für die neue Card.</p>
            <div class="clubcms-editor__template-links">
                <?php foreach ($templates as $key => $template): ?>
                    <?php
                    $query = ['template' => $key];
                    if ($currentCategoryId !== '') {
                        $query['category_id'] = $currentCategoryId;
                    }
                    if ($this->backToUrl !== '') {
                        $query['back_to'] = $this->backToUrl;
                    }
                    $url = add_query_arg($query, $baseUrl);
                    ?>
                    <a class="button button-secondary" href="<?php echo $this->escapeAttr($url); ?>"><?php echo $this->escapeHtml($template['label']); ?></a>
                <?php endforeach; ?>
            </div>
        </article>
        <?php

        return (string) ob_get_clean();
    }

    private function renderErrorNotice(): string
    {
        $error = $this->submissionHandler->getLastError();

        if ($error === null) {
            return '';
        }

        $fieldErrors = $this->submissionHandler->getLastFieldErrors();

        if ($fieldErrors === []) {
            return '<div class="notice notice-error is-dismissible"><p>' . $this->escapeHtml($error) . '</p></div>';
        }

        $items = '';

        foreach ($fieldErrors as $fieldError) {
            $items .= '<li>' . $this->escapeHtml($fieldError) . '</li>';
        }

        return '<div class="notice notice-error is-dismissible"><p>Bitte pruefe die markierten Felder.</p><ul>' . $items . '</ul></div>';
    }

    private function renderAccessDenied(): string
    {
        return '<div class="notice notice-warning"><p>Für diesen Bereich fehlen die erforderlichen Rechte.</p></div>';
    }

    private function getPresetCategoryId(): string
    {
        $categoryId = (string) ($_GET['category_id'] ?? '');

        if ($categoryId === '') {
            return '';
        }

        return $categoryId;
    }

    private function currentUrl(): string
    {
        $requestUri = $this->requestUri();
        $path = parse_url($requestUri, PHP_URL_PATH);
        $path = is_string($path) && $path !== '' ? $path : '/';

        return rtrim($this->homeUrl(), '/') . $path;
    }

    /**
     * @param array<string, string> $queryArgs
     */
    private function buildUrl(array $queryArgs): string
    {
        if ($this->backToUrl !== '' && ! array_key_exists('back_to', $queryArgs)) {
            $queryArgs['back_to'] = $this->backToUrl;
        }

        return add_query_arg($queryArgs, $this->currentUrl());
    }

    private function buildReturnUrl(array $queryArgs = []): string
    {
        $baseUrl = $this->backToUrl !== '' ? $this->backToUrl : $this->currentUrl();

        return add_query_arg($queryArgs, $baseUrl);
    }

    private function buildResetUrl(): string
    {
        $queryArgs = [];

        if ($this->backToUrl !== '') {
            $queryArgs['back_to'] = $this->backToUrl;
        }

        return add_query_arg($queryArgs, $this->currentUrl());
    }

    private function redirect(string $url): void
    {
        $redirect = $this->redirect ?? static function (string $target): void {
            if (function_exists('wp_safe_redirect')) {
                wp_safe_redirect($target);
            }
        };

        $redirect($url);
    }

    private function redirectOrDefer(string $url): bool
    {
        if ($this->headersAlreadySent()) {
            return false;
        }

        $this->redirect($url);

        return true;
    }

    private function terminate(): void
    {
        $terminate = $this->terminate ?? static function (): void {
            exit;
        };

        $terminate();
    }

    private function requestUri(): string
    {
        if ($this->requestUri !== null) {
            return (string) ($this->requestUri)();
        }

        return (string) ($_SERVER['REQUEST_URI'] ?? '');
    }

    private function homeUrl(): string
    {
        if ($this->homeUrl !== null) {
            return (string) ($this->homeUrl)();
        }

        if (function_exists('home_url')) {
            return (string) home_url('/');
        }

        return '/';
    }

    private function escapeHtml(string $value): string
    {
        if (function_exists('esc_html')) {
            return (string) esc_html($value);
        }

        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function escapeAttr(string $value): string
    {
        if (function_exists('esc_attr')) {
            return (string) esc_attr($value);
        }

        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function extractBackToUrl(array $attributes): string
    {
        $value = '';

        if (array_key_exists('back_to', $attributes) && is_string($attributes['back_to'])) {
            $value = trim($attributes['back_to']);
        } elseif (array_key_exists('backto', $attributes) && is_string($attributes['backto'])) {
            $value = trim($attributes['backto']);
        }

        if ($value !== '') {
            return $this->normalizeReturnUrl($value);
        }

        $requestBackTo = (string) ($_GET['back_to'] ?? '');

        if ($requestBackTo !== '') {
            return $this->normalizeReturnUrl($requestBackTo);
        }

        $postedBackTo = (string) ($_POST['back_to'] ?? '');

        if ($postedBackTo !== '') {
            return $this->normalizeReturnUrl($postedBackTo);
        }

        return '';
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function extractTemplateKey(array $attributes): string
    {
        foreach (['template', 'vorlage', 'preset'] as $key) {
            if (! array_key_exists($key, $attributes)) {
                continue;
            }

            $value = $attributes[$key];

            if (is_string($value)) {
                $value = trim($value);

                if ($value !== '') {
                    return $this->normalizeTemplateKey($value);
                }
            }
        }

        $requestTemplate = (string) ($_GET['template'] ?? '');

        if ($requestTemplate !== '') {
            return $this->normalizeTemplateKey($requestTemplate);
        }

        $postedTemplate = (string) ($_POST['template'] ?? '');

        if ($postedTemplate !== '') {
            return $this->normalizeTemplateKey($postedTemplate);
        }

        return '';
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function extractDuplicateCardId(array $attributes): string
    {
        if (array_key_exists('duplicate_card', $attributes) && is_string($attributes['duplicate_card'])) {
            $value = trim($attributes['duplicate_card']);

            if ($value !== '') {
                return $this->normalizeTemplateKey($value);
            }
        }

        $requestValue = (string) ($_GET['duplicate_card'] ?? '');

        if ($requestValue !== '') {
            return $this->normalizeTemplateKey($requestValue);
        }

        $postedValue = (string) ($_POST['duplicate_card'] ?? '');

        if ($postedValue !== '') {
            return $this->normalizeTemplateKey($postedValue);
        }

        return '';
    }

    private function normalizeTemplateKey(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_-]/', '', $value) ?? '';

        return trim($value, "_-");
    }

    private function postedString(string $key, string $fallback = ''): string
    {
        if (! array_key_exists($key, $_POST)) {
            return $fallback;
        }

        $value = $_POST[$key];

        if (! is_string($value)) {
            return $fallback;
        }

        $value = trim($value);

        return $value === '' ? $fallback : $value;
    }

    private function postedBoolean(string $key, bool $fallback = false): bool
    {
        if (! array_key_exists($key, $_POST)) {
            return $fallback;
        }

        return ! empty($_POST[$key]);
    }

    private function isSubmittedCardPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['clubcms_form'] ?? '') === 'card';
    }

    /**
     * @return array<string, array{label: string, fields: array<string, mixed>, status: string, visibility: string, isStatic: bool, position: int}>
     */
    private function templateDefinitions(): array
    {
        return [
            'standard' => [
                'label' => 'Standard',
                'fields' => [
                    'headline' => '',
                    'teaser' => '',
                    'body' => '',
                ],
                'status' => 'draft',
                'visibility' => 'public',
                'isStatic' => false,
                'position' => 0,
            ],
            'news' => [
                'label' => 'News',
                'fields' => [
                    'headline' => '',
                    'teaser' => '',
                    'body' => '',
                    'link_text' => '',
                    'link_url' => '',
                ],
                'status' => 'published',
                'visibility' => 'public',
                'isStatic' => false,
                'position' => 0,
            ],
            'event' => [
                'label' => 'Veranstaltung',
                'fields' => [
                    'date' => '',
                    'time' => '',
                    'location' => '',
                    'teaser' => '',
                    'body' => '',
                ],
                'status' => 'published',
                'visibility' => 'members',
                'isStatic' => false,
                'position' => 0,
            ],
        ];
    }

    private function applyTemplate(Card $card, string $templateKey): Card
    {
        $templates = $this->templateDefinitions();
        $template = $templates[$templateKey] ?? $templates['standard'];

        return new Card(
            $card->id,
            $card->title,
            $card->categoryId,
            $template['fields'],
            $this->normalizeCardStatus($template['status']),
            $this->normalizeVisibility($template['visibility']),
            $template['isStatic'],
            $template['position'],
            $card->publishedAt
        );
    }

    private function normalizeCardStatus(string $value): \ClubCMS\Domain\CardStatus
    {
        return \ClubCMS\Domain\CardStatus::tryFrom($value) ?? \ClubCMS\Domain\CardStatus::Draft;
    }

    private function normalizeVisibility(string $value): \ClubCMS\Domain\Visibility
    {
        return \ClubCMS\Domain\Visibility::tryFrom($value) ?? \ClubCMS\Domain\Visibility::Public;
    }

    private function normalizeReturnUrl(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        if (str_starts_with($value, '/')) {
            return $value;
        }

        $scheme = parse_url($value, PHP_URL_SCHEME);

        if (is_string($scheme) && $scheme !== '') {
            return filter_var($value, FILTER_VALIDATE_URL) ? $value : '';
        }

        return '';
    }

    private function headersAlreadySent(): bool
    {
        if ($this->headersSent !== null) {
            return (bool) ($this->headersSent)();
        }

        return function_exists('headers_sent') && headers_sent();
    }

}
