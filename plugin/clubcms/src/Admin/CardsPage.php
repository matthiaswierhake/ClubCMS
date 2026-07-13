<?php

declare(strict_types=1);

namespace ClubCMS\Admin;

use ClubCMS\Domain\Card;
use ClubCMS\Domain\Category;
use ClubCMS\Repository\CardRepositoryInterface;
use ClubCMS\Repository\CategoryRepositoryInterface;

final class CardsPage
{
    public function __construct(
        private readonly CardRepositoryInterface $cardRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly CardSubmissionHandler $submissionHandler,
    ) {
    }

    public function render(): void
    {
        $this->handleSubmit();
        $editingCard = $this->getEditingCard();

        echo '<div class="wrap">';
        echo '<h1>Cards</h1>';
        echo $this->renderStatusNotice();
        echo $this->renderForm($editingCard);
        echo $this->renderList();
        echo '</div>';
    }

    private function handleSubmit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['clubcms_form'] ?? '') !== 'card') {
            return;
        }

        check_admin_referer('clubcms_save_card');

        if (! current_user_can('manage_options')) {
            wp_die('Insufficient permissions.');
        }

        $action = (string) ($_POST['clubcms_action'] ?? 'save');

        if ($action === 'delete') {
            if (! $this->submissionHandler->handleDelete($_POST)) {
                return;
            }

            wp_safe_redirect(add_query_arg(['page' => 'clubcms-cards', 'deleted' => '1'], admin_url('admin.php')));
            exit;
        }

        if (! $this->submissionHandler->handleCard($_POST)) {
            return;
        }

        wp_safe_redirect(add_query_arg(['page' => 'clubcms-cards', 'saved' => '1'], admin_url('admin.php')));
        exit;
    }

    private function getEditingCard(): ?Card
    {
        $id = (string) ($_GET['edit_card'] ?? '');

        if ($id === '') {
            return null;
        }

        return $this->cardRepository->getById($id);
    }

    private function renderStatusNotice(): string
    {
        if (! isset($_GET['saved']) && ! isset($_GET['deleted'])) {
            return '';
        }

        $message = isset($_GET['deleted']) ? 'Card wurde gelöscht.' : 'Card wurde gespeichert.';

        return '<div class="notice notice-success is-dismissible"><p>' . esc_html($message) . '</p></div>';
    }

    private function renderForm(?Card $editingCard): string
    {
        $card = $editingCard ?? new Card('', '', '', [], publishedAt: null);
        $isEditMode = $editingCard !== null;

        $categories = $this->categoryRepository->all();

        ob_start();
        ?>
        <h2><?php echo $isEditMode ? 'Card bearbeiten' : 'Card anlegen'; ?></h2>
        <form method="post">
            <?php wp_nonce_field('clubcms_save_card'); ?>
            <input type="hidden" name="clubcms_form" value="card" />
            <input type="hidden" name="clubcms_action" value="save" />
            <input type="hidden" name="original_id" value="<?php echo esc_attr($card->id); ?>" />
            <table class="form-table" role="presentation">
                <tr>
                    <th><label for="card_id">ID</label></th>
                    <td><input name="id" id="card_id" type="text" class="regular-text" required value="<?php echo esc_attr($card->id); ?>"></td>
                </tr>
                <tr>
                    <th><label for="card_title">Titel</label></th>
                    <td><input name="title" id="card_title" type="text" class="regular-text" required value="<?php echo esc_attr($card->title); ?>"></td>
                </tr>
                <tr>
                    <th><label for="category_id">Kategorie</label></th>
                    <td>
                        <select name="category_id" id="category_id">
                            <option value="">Bitte wählen</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo esc_attr($category->id); ?>" <?php echo $category->id === $card->categoryId ? 'selected' : ''; ?>>
                                    <?php echo esc_html($category->label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="status">Status</label></th>
                    <td>
                        <select name="status" id="status">
                            <option value="draft" <?php echo $card->status->value === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?php echo $card->status->value === 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="archived" <?php echo $card->status->value === 'archived' ? 'selected' : ''; ?>>Archived</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="visibility">Sichtbarkeit</label></th>
                    <td>
                        <select name="visibility" id="visibility">
                            <option value="public" <?php echo $card->visibility->value === 'public' ? 'selected' : ''; ?>>Public</option>
                            <option value="members" <?php echo $card->visibility->value === 'members' ? 'selected' : ''; ?>>Members</option>
                            <option value="editorial" <?php echo $card->visibility->value === 'editorial' ? 'selected' : ''; ?>>Editorial</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="position">Position</label></th>
                    <td><input name="position" id="position" type="number" class="small-text" value="<?php echo esc_attr((string) $card->position); ?>"></td>
                </tr>
                <tr>
                    <th><label for="published_at">Veröffentlicht am</label></th>
                    <td><input name="published_at" id="published_at" type="text" class="regular-text" placeholder="2026-07-13 10:00:00" value="<?php echo esc_attr($card->publishedAt?->format('Y-m-d H:i:s') ?? ''); ?>"></td>
                </tr>
                <tr>
                    <th><label for="is_static">Statisch</label></th>
                    <td><label><input name="is_static" id="is_static" type="checkbox" value="1" <?php echo $card->isStatic ? 'checked' : ''; ?>> Statische Card</label></td>
                </tr>
                <tr>
                    <th><label for="fields_json">Felder als JSON</label></th>
                    <td><textarea name="fields_json" id="fields_json" class="large-text code" rows="10"><?php echo esc_html((string) json_encode($card->fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button($isEditMode ? 'Card aktualisieren' : 'Card speichern'); ?>
        </form>
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
        echo '<h2>Vorhandene Cards</h2>';

        if ($items === []) {
            echo '<p>Noch keine Cards angelegt.</p>';
            return (string) ob_get_clean();
        }

        echo '<table class="widefat striped">';
        echo '<thead><tr><th>ID</th><th>Titel</th><th>Kategorie</th><th>Status</th><th>Sichtbarkeit</th><th>Aktionen</th></tr></thead><tbody>';

        foreach ($items as $item) {
            $categoryLabel = $categories[$item->categoryId]->label ?? $item->categoryId;
            echo '<tr>';
            echo '<td>' . esc_html($item->id) . '</td>';
            echo '<td>' . esc_html($item->title) . '</td>';
            echo '<td>' . esc_html($categoryLabel) . '</td>';
            echo '<td>' . esc_html($item->status->value) . '</td>';
            echo '<td>' . esc_html($item->visibility->value) . '</td>';
            echo '<td>' . $this->renderEditLink($item->id) . ' ' . $this->renderDeleteForm($item->id) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        return (string) ob_get_clean();
    }

    private function renderEditLink(string $id): string
    {
        $url = add_query_arg([
            'page' => 'clubcms-cards',
            'edit_card' => $id,
        ], admin_url('admin.php'));

        return '<a class="button button-small" href="' . esc_attr($url) . '">Bearbeiten</a>';
    }

    private function renderDeleteForm(string $id): string
    {
        ob_start();
        ?>
        <form method="post" style="display:inline;">
            <?php wp_nonce_field('clubcms_save_card'); ?>
            <input type="hidden" name="clubcms_form" value="card" />
            <input type="hidden" name="clubcms_action" value="delete" />
            <input type="hidden" name="id" value="<?php echo esc_attr($id); ?>" />
            <?php submit_button('Löschen', 'delete', 'submit', false); ?>
        </form>
        <?php
        return (string) ob_get_clean();
    }
}
