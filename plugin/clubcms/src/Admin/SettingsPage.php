<?php

declare(strict_types=1);

namespace ClubCMS\Admin;

use ClubCMS\Domain\Category;
use ClubCMS\Domain\FieldDefinition;
use ClubCMS\Repository\CategoryRepository;
use ClubCMS\Repository\FieldDefinitionRepository;

final class SettingsPage
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly FieldDefinitionRepository $fieldDefinitionRepository,
    ) {
    }

    public function renderCategories(): void
    {
        $this->handleCategorySubmit();

        echo '<div class="wrap">';
        echo '<h1>Kategorien</h1>';
        echo $this->renderCategoryForm();
        echo $this->renderCategoryList();
        echo '</div>';
    }

    public function renderFieldDefinitions(): void
    {
        $this->handleFieldDefinitionSubmit();

        echo '<div class="wrap">';
        echo '<h1>Felddefinitionen</h1>';
        echo $this->renderFieldDefinitionForm();
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

        $id = sanitize_key((string) ($_POST['id'] ?? ''));
        $label = sanitize_text_field((string) ($_POST['label'] ?? ''));
        $slug = sanitize_title((string) ($_POST['slug'] ?? ''));
        $sortMode = sanitize_key((string) ($_POST['sort_mode'] ?? 'date'));
        $fieldDefinitionIds = $this->normalizeIdList((string) ($_POST['field_definition_ids'] ?? ''));

        if ($id === '' || $label === '' || $slug === '') {
            return;
        }

        $this->categoryRepository->save(
            new Category($id, $label, $slug, $sortMode, $fieldDefinitionIds)
        );

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

        $id = sanitize_key((string) ($_POST['id'] ?? ''));
        $label = sanitize_text_field((string) ($_POST['label'] ?? ''));
        $json = (string) ($_POST['fields_json'] ?? '[]');
        $fields = json_decode($json, true);

        if (! is_array($fields)) {
            $fields = [];
        }

        if ($id === '' || $label === '') {
            return;
        }

        $this->fieldDefinitionRepository->save(
            new FieldDefinition($id, $label, $fields)
        );

        wp_safe_redirect(add_query_arg(['page' => 'clubcms-field-definitions', 'saved' => '1'], admin_url('admin.php')));
        exit;
    }

    private function renderCategoryForm(): string
    {
        ob_start();
        ?>
        <form method="post">
            <?php wp_nonce_field('clubcms_save_category'); ?>
            <input type="hidden" name="clubcms_form" value="category" />
            <table class="form-table" role="presentation">
                <tr>
                    <th><label for="id">ID</label></th>
                    <td><input name="id" id="id" type="text" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="label">Bezeichnung</label></th>
                    <td><input name="label" id="label" type="text" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="slug">Slug</label></th>
                    <td><input name="slug" id="slug" type="text" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="sort_mode">Sortierung</label></th>
                    <td>
                        <select name="sort_mode" id="sort_mode">
                            <option value="date">Datum</option>
                            <option value="manual">Manuell</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="field_definition_ids">Felddefinitions-IDs</label></th>
                    <td><input name="field_definition_ids" id="field_definition_ids" type="text" class="regular-text" placeholder="fd-news, fd-events"></td>
                </tr>
            </table>
            <?php submit_button('Kategorie speichern'); ?>
        </form>
        <?php
        return (string) ob_get_clean();
    }

    private function renderFieldDefinitionForm(): string
    {
        ob_start();
        ?>
        <form method="post">
            <?php wp_nonce_field('clubcms_save_field_definition'); ?>
            <input type="hidden" name="clubcms_form" value="field_definition" />
            <table class="form-table" role="presentation">
                <tr>
                    <th><label for="id">ID</label></th>
                    <td><input name="id" id="id" type="text" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="label">Bezeichnung</label></th>
                    <td><input name="label" id="label" type="text" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="fields_json">Felder als JSON</label></th>
                    <td><textarea name="fields_json" id="fields_json" class="large-text code" rows="8">[]</textarea></td>
                </tr>
            </table>
            <?php submit_button('Felddefinition speichern'); ?>
        </form>
        <?php
        return (string) ob_get_clean();
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
        echo '<thead><tr><th>ID</th><th>Bezeichnung</th><th>Slug</th><th>Sortierung</th><th>Felddefinitionen</th></tr></thead><tbody>';

        foreach ($items as $item) {
            echo '<tr>';
            echo '<td>' . esc_html($item->id) . '</td>';
            echo '<td>' . esc_html($item->label) . '</td>';
            echo '<td>' . esc_html($item->slug) . '</td>';
            echo '<td>' . esc_html($item->sortMode) . '</td>';
            echo '<td>' . esc_html(implode(', ', $item->fieldDefinitionIds)) . '</td>';
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
        echo '<thead><tr><th>ID</th><th>Bezeichnung</th><th>Felder</th></tr></thead><tbody>';

        foreach ($items as $item) {
            echo '<tr>';
            echo '<td>' . esc_html($item->id) . '</td>';
            echo '<td>' . esc_html($item->label) . '</td>';
            echo '<td>' . esc_html((string) count($item->fields)) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        return (string) ob_get_clean();
    }

    /**
     * @return array<int, string>
     */
    private function normalizeIdList(string $value): array
    {
        $parts = preg_split('/\s*,\s*/', trim($value)) ?: [];
        $parts = array_map('sanitize_key', $parts);

        return array_values(array_filter($parts, static fn (string $item): bool => $item !== ''));
    }
}
