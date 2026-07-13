<?php

declare(strict_types=1);

namespace ClubCMS\Admin;

use ClubCMS\Repository\CategoryRepository;
use ClubCMS\Repository\FieldDefinitionRepository;

final class Dashboard
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly FieldDefinitionRepository $fieldDefinitionRepository,
    ) {
    }

    public function render(): void
    {
        $categoryCount = count($this->categoryRepository->all());
        $fieldDefinitionCount = count($this->fieldDefinitionRepository->all());

        echo '<div class="wrap">';
        echo '<h1>ClubCMS</h1>';
        echo '<p>Grundgerüst geladen. Kategorien und Felddefinitionen können jetzt gespeichert werden.</p>';
        echo '<ul>';
        echo '<li>Kategorien: ' . esc_html((string) $categoryCount) . '</li>';
        echo '<li>Felddefinitionen: ' . esc_html((string) $fieldDefinitionCount) . '</li>';
        echo '</ul>';
        echo '</div>';
    }
}
