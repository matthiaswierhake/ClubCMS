<?php

declare(strict_types=1);

namespace ClubCMS\Admin;

use ClubCMS\Domain\Category;
use ClubCMS\Domain\FieldDefinition;
use ClubCMS\Repository\CategoryRepositoryInterface;
use ClubCMS\Repository\FieldDefinitionRepositoryInterface;

final class SettingsPage
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly FieldDefinitionRepositoryInterface $fieldDefinitionRepository,
        private readonly SettingsSubmissionHandler $submissionHandler,
    ) {
    }

    public function renderCategories(): void
    {
        $this->handleCategorySubmit();

        $editingCategory = $this->getEditingCategory();

        echo '<div class="wrap">';
        echo '<h1>Kategorien</h1>';
        echo $this->renderStatusNotice('clubcms_categories_status');
        echo $this->renderCategoryForm($editingCategory);
        echo $this->renderCategoryList();
        echo '</div>';
    }

    public function renderFieldDefinitions(): void
    {
        $this->handleFieldDefinitionSubmit();

        $editingFieldDefinition = $this->getEditingFieldDefinition();

        echo '<div class="wrap">';
        echo '<h1>Felddefinitionen</h1>';
        echo $this->renderStatusNotice('clubcms_field_definitions_status');
        echo $this->renderFieldDefinitionNotice();
        echo $this->renderFieldDefinitionForm($editingFieldDefinition);
        echo $this->renderFieldDefinitionList();
        echo '</div>';
    }

    private function handleCategorySubmit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['clubcms_form'] ?? '') !== 'category') {
            return;
        }

        check_admin_referer('clubcms_save_category');

        if (! current_user_can('manage_options')) {
            wp_die('Insufficient permissions.');
        }

        $action = (string) ($_POST['clubcms_action'] ?? 'save');

        if ($action === 'delete') {
            if (! $this->submissionHandler->handleCategoryDelete($_POST)) {
                return;
            }

            wp_safe_redirect(add_query_arg(['page' => 'clubcms-categories', 'deleted' => '1'], admin_url('admin.php')));
            exit;
        }

        if (! $this->submissionHandler->handleCategory($_POST)) {
            return;
        }

        wp_safe_redirect(add_query_arg(['page' => 'clubcms-categories', 'saved' => '1'], admin_url('admin.php')));
        exit;
    }

    private function handleFieldDefinitionSubmit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['clubcms_form'] ?? '') !== 'field_definition') {
            return;
        }

        check_admin_referer('clubcms_save_field_definition');

        if (! current_user_can('manage_options')) {
            wp_die('Insufficient permissions.');
        }

        $action = (string) ($_POST['clubcms_action'] ?? 'save');

        if ($action === 'delete') {
            if (! $this->submissionHandler->handleFieldDefinitionDelete($_POST)) {
                return;
            }

            wp_safe_redirect(add_query_arg(['page' => 'clubcms-field-definitions', 'deleted' => '1'], admin_url('admin.php')));
            exit;
        }

        if (! $this->submissionHandler->handleFieldDefinition($_POST)) {
            return;
        }

        wp_safe_redirect(add_query_arg(['page' => 'clubcms-field-definitions', 'saved' => '1'], admin_url('admin.php')));
        exit;
    }

    private function getEditingCategory(): ?Category
    {
        $id = (string) ($_GET['edit_category'] ?? '');

        if ($id === '') {
            return null;
        }

        return $this->categoryRepository->getById($id);
    }

    private function getEditingFieldDefinition(): ?FieldDefinition
    {
        $id = (string) ($_GET['edit_field_definition'] ?? '');

        if ($id === '') {
            return null;
        }

        return $this->fieldDefinitionRepository->getById($id);
    }

    private function renderCategoryForm(?Category $editingCategory): string
    {
        $category = $editingCategory ?? new Category('', '', '', 'date', []);
        $isEditMode = $editingCategory !== null;

        ob_start();
        ?>
        <h2><?php echo $isEditMode ? 'Kategorie bearbeiten' : 'Kategorie anlegen'; ?></h2>
        <form method="post">
            <?php wp_nonce_field('clubcms_save_category'); ?>
            <input type="hidden" name="clubcms_form" value="category" />
            <input type="hidden" name="clubcms_action" value="save" />
            <input type="hidden" name="original_id" value="<?php echo esc_attr($category->id); ?>" />
            <table class="form-table" role="presentation">
                <tr>
                    <th><label for="category_id">ID</label></th>
                    <td><input name="id" id="category_id" type="text" class="regular-text" required value="<?php echo esc_attr($category->id); ?>"></td>
                </tr>
                <tr>
                    <th><label for="category_label">Bezeichnung</label></th>
                    <td><input name="label" id="category_label" type="text" class="regular-text" required value="<?php echo esc_attr($category->label); ?>"></td>
                </tr>
                <tr>
                    <th><label for="category_slug">Slug</label></th>
                    <td><input name="slug" id="category_slug" type="text" class="regular-text" required value="<?php echo esc_attr($category->slug); ?>"></td>
                </tr>
                <tr>
                    <th><label for="sort_mode">Sortierung</label></th>
                    <td>
                        <select name="sort_mode" id="sort_mode">
                            <option value="date" <?php echo $category->sortMode === 'date' ? 'selected' : ''; ?>>Datum</option>
                            <option value="manual" <?php echo $category->sortMode === 'manual' ? 'selected' : ''; ?>>Manuell</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="field_definition_ids">Felddefinitions-IDs</label></th>
                    <td><input name="field_definition_ids" id="field_definition_ids" type="text" class="regular-text" placeholder="fd-news, fd-events" value="<?php echo esc_attr(implode(', ', $category->fieldDefinitionIds)); ?>"></td>
                </tr>
            </table>
            <?php submit_button($isEditMode ? 'Kategorie aktualisieren' : 'Kategorie speichern'); ?>
        </form>
        <?php
        return (string) ob_get_clean();
    }

    private function renderFieldDefinitionForm(?FieldDefinition $editingFieldDefinition): string
    {
        $definition = $editingFieldDefinition ?? new FieldDefinition('', '', []);
        $isEditMode = $editingFieldDefinition !== null;

        ob_start();
        ?>
        <h2><?php echo $isEditMode ? 'Felddefinition bearbeiten' : 'Felddefinition anlegen'; ?></h2>
        <form method="post">
            <?php wp_nonce_field('clubcms_save_field_definition'); ?>
            <input type="hidden" name="clubcms_form" value="field_definition" />
            <input type="hidden" name="clubcms_action" value="save" />
            <input type="hidden" name="original_id" value="<?php echo esc_attr($definition->id); ?>" />
            <table class="form-table" role="presentation">
                <tr>
                    <th><label for="field_definition_id">ID</label></th>
                    <td><input name="id" id="field_definition_id" type="text" class="regular-text" required value="<?php echo esc_attr($definition->id); ?>"></td>
                </tr>
                <tr>
                    <th><label for="field_definition_label">Bezeichnung</label></th>
                    <td><input name="label" id="field_definition_label" type="text" class="regular-text" required value="<?php echo esc_attr($definition->label); ?>"></td>
                </tr>
                <tr>
                    <th><label for="fields_json">Felder als JSON</label></th>
                    <td><textarea name="fields_json" id="fields_json" class="large-text code" rows="8"><?php echo esc_html((string) json_encode($definition->fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button($isEditMode ? 'Felddefinition aktualisieren' : 'Felddefinition speichern'); ?>
        </form>
        <?php
        return (string) ob_get_clean();
    }

    private function renderFieldDefinitionNotice(): string
    {
        $error = $this->submissionHandler->getLastError();

        if ($error === null) {
            return '';
        }

        return '<div class="notice notice-error is-dismissible"><p>' . esc_html($error) . '</p></div>';
    }

    private function renderStatusNotice(string $param): string
    {
        if (! isset($_GET[$param])) {
            return '';
        }

        $message = match ((string) $_GET[$param]) {
            'saved' => 'Änderungen wurden gespeichert.',
            'deleted' => 'Eintrag wurde gelöscht.',
            default => '',
        };

        if ($message === '') {
            return '';
        }

        return '<div class="notice notice-success is-dismissible"><p>' . esc_html($message) . '</p></div>';
    }

    private function renderCategoryList(): string
    {
        $items = $this->categoryRepository->all();

        ob_start();
        echo '<h2>Vorhandene Kategorien</h2>';

        if ($items === []) {
            echo '<p>Noch keine Kategorien angelegt.</p>';
            return (string) ob_get_clean();
        }

        echo '<table class="widefat striped">';
        echo '<thead><tr><th>ID</th><th>Bezeichnung</th><th>Slug</th><th>Sortierung</th><th>Felddefinitionen</th><th>Aktionen</th></tr></thead><tbody>';

        foreach ($items as $item) {
            echo '<tr>';
            echo '<td>' . esc_html($item->id) . '</td>';
            echo '<td>' . esc_html($item->label) . '</td>';
            echo '<td>' . esc_html($item->slug) . '</td>';
            echo '<td>' . esc_html($item->sortMode) . '</td>';
            echo '<td>' . esc_html(implode(', ', $item->fieldDefinitionIds)) . '</td>';
            echo '<td>' . $this->renderEditLink('clubcms-categories', 'edit_category', $item->id, 'Bearbeiten') . ' ' . $this->renderDeleteForm('category', $item->id) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        return (string) ob_get_clean();
    }

    private function renderFieldDefinitionList(): string
    {
        $items = $this->fieldDefinitionRepository->all();

        ob_start();
        echo '<h2>Vorhandene Felddefinitionen</h2>';

        if ($items === []) {
            echo '<p>Noch keine Felddefinitionen angelegt.</p>';
            return (string) ob_get_clean();
        }

        echo '<table class="widefat striped">';
        echo '<thead><tr><th>ID</th><th>Bezeichnung</th><th>Felder</th><th>Aktionen</th></tr></thead><tbody>';

        foreach ($items as $item) {
            echo '<tr>';
            echo '<td>' . esc_html($item->id) . '</td>';
            echo '<td>' . esc_html($item->label) . '</td>';
            echo '<td>' . esc_html((string) count($item->fields)) . '</td>';
            echo '<td>' . $this->renderEditLink('clubcms-field-definitions', 'edit_field_definition', $item->id, 'Bearbeiten') . ' ' . $this->renderDeleteForm('field_definition', $item->id) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        return (string) ob_get_clean();
    }

    private function renderEditLink(string $page, string $param, string $id, string $label): string
    {
        $url = add_query_arg([
            'page' => $page,
            $param => $id,
        ], admin_url('admin.php'));

        return '<a class="button button-small" href="' . esc_attr($url) . '">' . esc_html($label) . '</a>';
    }

    private function renderDeleteForm(string $formType, string $id): string
    {
        $nonceAction = $formType === 'category' ? 'clubcms_save_category' : 'clubcms_save_field_definition';
        $buttonLabel = 'Löschen';

        ob_start();
        ?>
        <form method="post" style="display:inline;">
            <?php wp_nonce_field($nonceAction); ?>
            <input type="hidden" name="clubcms_form" value="<?php echo esc_attr($formType); ?>" />
            <input type="hidden" name="clubcms_action" value="delete" />
            <input type="hidden" name="id" value="<?php echo esc_attr($id); ?>" />
            <?php submit_button($buttonLabel, 'delete', 'submit', false); ?>
        </form>
        <?php
        return (string) ob_get_clean();
    }
}
