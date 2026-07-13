<?php

declare(strict_types=1);

namespace ClubCMS\Admin;

use ClubCMS\Domain\Category;
use ClubCMS\Domain\FieldDefinition;
use ClubCMS\Infrastructure\EditorSettingsStorageInterface;
use ClubCMS\Repository\CategoryRepositoryInterface;
use ClubCMS\Repository\FieldDefinitionRepositoryInterface;

final class SettingsSubmissionHandler
{
    private ?string $lastError = null;

    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly FieldDefinitionRepositoryInterface $fieldDefinitionRepository,
        private readonly ?EditorSettingsStorageInterface $editorSettingsStorage = null,
    ) {
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * @param array<string, mixed> $post
     */
    public function handleCategory(array $post): bool
    {
        $this->lastError = null;

        $id = $this->sanitizeKey((string) ($post['id'] ?? ''));
        $originalId = $this->sanitizeKey((string) ($post['original_id'] ?? $id));
        $label = $this->sanitizeText((string) ($post['label'] ?? ''));
        $slug = $this->sanitizeTitle((string) ($post['slug'] ?? ''));
        $sortMode = Category::normalizeSortMode($this->sanitizeKey((string) ($post['sort_mode'] ?? 'date_desc')));
        $fieldDefinitionIds = $this->normalizeIdList((string) ($post['field_definition_ids'] ?? ''));

        if ($id === '' || $label === '' || $slug === '') {
            return false;
        }

        if ($originalId !== '' && $originalId !== $id) {
            $this->categoryRepository->delete($originalId);
        }

        $this->categoryRepository->save(
            new Category($id, $label, $slug, $sortMode, $fieldDefinitionIds)
        );

        return true;
    }

    /**
     * @param array<string, mixed> $post
     */
    public function handleFieldDefinition(array $post): bool
    {
        $this->lastError = null;

        $id = $this->sanitizeKey((string) ($post['id'] ?? ''));
        $originalId = $this->sanitizeKey((string) ($post['original_id'] ?? $id));
        $label = $this->sanitizeText((string) ($post['label'] ?? ''));
        $json = (string) ($post['fields_json'] ?? '[]');

        try {
            $fields = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            $this->lastError = 'Die Felddefinition enthält ungültiges JSON.';

            return false;
        }

        if ($id === '' || $label === '') {
            $this->lastError = 'ID und Bezeichnung sind erforderlich.';

            return false;
        }

        if (! is_array($fields)) {
            $this->lastError = 'Felder müssen als JSON-Array angegeben werden.';

            return false;
        }

        if ($originalId !== '' && $originalId !== $id) {
            $this->fieldDefinitionRepository->delete($originalId);
        }

        $this->fieldDefinitionRepository->save(
            new FieldDefinition($id, $label, $fields)
        );

        return true;
    }

    /**
     * @param array<string, mixed> $post
     */
    public function handleCategoryDelete(array $post): bool
    {
        $this->lastError = null;

        $id = $this->sanitizeKey((string) ($post['id'] ?? ''));

        if ($id === '') {
            return false;
        }

        $this->categoryRepository->delete($id);

        return true;
    }

    /**
     * @param array<string, mixed> $post
     */
    public function handleFieldDefinitionDelete(array $post): bool
    {
        $this->lastError = null;

        $id = $this->sanitizeKey((string) ($post['id'] ?? ''));

        if ($id === '') {
            return false;
        }

        $this->fieldDefinitionRepository->delete($id);

        return true;
    }

    /**
     * @param array<string, mixed> $post
     */
    public function handleEditorSettings(array $post): bool
    {
        $this->lastError = null;

        if ($this->editorSettingsStorage === null) {
            return false;
        }

        $editorUrl = $this->normalizeEditorUrl((string) ($post['editor_url'] ?? ''));

        if ($editorUrl === null) {
            $this->lastError = 'Die Editor-URL ist ungültig.';

            return false;
        }

        $this->editorSettingsStorage->saveEditorUrl($editorUrl);

        return true;
    }

    public function getEditorUrl(): string
    {
        if ($this->editorSettingsStorage === null) {
            return '';
        }

        return $this->editorSettingsStorage->getEditorUrl();
    }

    /**
     * @return array<int, string>
     */
    private function normalizeIdList(string $value): array
    {
        $parts = preg_split('/\s*,\s*/', trim($value)) ?: [];
        $parts = array_map(fn (string $item): string => $this->sanitizeKey($item), $parts);

        return array_values(array_filter($parts, static fn (string $item): bool => $item !== ''));
    }

    private function sanitizeKey(string $value): string
    {
        if (function_exists('sanitize_key')) {
            return (string) sanitize_key($value);
        }

        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9_-]/', '', $value) ?? '';

        return trim($value, "_-");
    }

    private function sanitizeText(string $value): string
    {
        if (function_exists('sanitize_text_field')) {
            return (string) sanitize_text_field($value);
        }

        $value = strip_tags($value);
        $value = preg_replace('/[\r\n\t ]+/', ' ', $value) ?? $value;

        return trim($value);
    }

    private function sanitizeTitle(string $value): string
    {
        if (function_exists('sanitize_title')) {
            return (string) sanitize_title($value);
        }

        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';

        return trim($value, '-');
    }

    private function normalizeEditorUrl(string $value): ?string
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
            return filter_var($value, FILTER_VALIDATE_URL) ? $value : null;
        }

        return null;
    }
}
