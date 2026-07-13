<?php

declare(strict_types=1);

namespace ClubCMS\Admin;

use ClubCMS\Infrastructure\EditorSettingsStorageInterface;
use ClubCMS\Repository\CategoryRepositoryInterface;
use ClubCMS\Repository\FieldDefinitionRepositoryInterface;

final class Dashboard
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly FieldDefinitionRepositoryInterface $fieldDefinitionRepository,
        private readonly ?EditorSettingsStorageInterface $editorSettingsStorage = null,
    ) {
    }

    public function render(): void
    {
        $categoryCount = count($this->categoryRepository->all());
        $fieldDefinitionCount = count($this->fieldDefinitionRepository->all());

        echo '<div class="wrap">';
        echo '<h1>ClubCMS</h1>';
        echo '<p>Grundgerüst geladen. Kategorien und Felddefinitionen können jetzt gespeichert werden.</p>';
        echo $this->renderEditorTile();
        echo '<ul>';
        echo '<li>Kategorien: ' . esc_html((string) $categoryCount) . '</li>';
        echo '<li>Felddefinitionen: ' . esc_html((string) $fieldDefinitionCount) . '</li>';
        echo '</ul>';
        echo '</div>';
    }

    private function renderEditorTile(): string
    {
        $editorUrl = $this->editorSettingsStorage?->getEditorUrl() ?? '';

        if ($editorUrl === '') {
            return '<div class="card" style="max-width: 520px; margin: 16px 0;">'
                . '<h2>Frontend-Editor</h2>'
                . '<p>Unter „Einstellungen“ die Editor-URL hinterlegen.</p>'
                . '</div>';
        }

        return '<div class="card" style="max-width: 520px; margin: 16px 0;">'
            . '<h2>Frontend-Editor</h2>'
            . '<p>Direkt zum zentralen Redaktions-Editor wechseln.</p>'
            . '<p><a class="button button-primary" href="' . esc_attr($editorUrl) . '">Editor öffnen</a></p>'
            . '</div>';
    }
}
