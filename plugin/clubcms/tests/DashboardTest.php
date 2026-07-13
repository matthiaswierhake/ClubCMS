<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Admin\Dashboard;
use ClubCMS\Domain\Category;
use ClubCMS\Domain\FieldDefinition;
use ClubCMS\Infrastructure\EditorSettingsStorageInterface;
use ClubCMS\Repository\CategoryRepositoryInterface;
use ClubCMS\Repository\FieldDefinitionRepositoryInterface;
use RuntimeException;

final class DashboardTest
{
    public function run(): void
    {
        $this->itRendersAFrontendEditorTileWhenTheUrlIsConfigured();
        $this->itShowsASetupHintWhenNoUrlIsConfigured();
    }

    private function itRendersAFrontendEditorTileWhenTheUrlIsConfigured(): void
    {
        $dashboard = new Dashboard(
            new DashboardCategoryRepository([
                new Category('cat-news', 'News', 'news'),
            ]),
            new DashboardFieldDefinitionRepository([
                new FieldDefinition('fd-news', 'News', []),
            ]),
            new DashboardEditorSettingsStorage('/clubcms-editor/')
        );

        $html = $this->captureOutput(static function () use ($dashboard): void {
            $dashboard->render();
        });

        $this->assertContains('Frontend-Editor', $html, 'The editor tile should be rendered.');
        $this->assertContains('Editor öffnen', $html, 'The editor tile should contain a link.');
        $this->assertContains('/clubcms-editor/', $html, 'The configured editor URL should be used.');
    }

    private function itShowsASetupHintWhenNoUrlIsConfigured(): void
    {
        $dashboard = new Dashboard(
            new DashboardCategoryRepository(),
            new DashboardFieldDefinitionRepository()
        );

        $html = $this->captureOutput(static function () use ($dashboard): void {
            $dashboard->render();
        });

        $this->assertContains('Editor-URL hinterlegen', $html, 'The dashboard should hint at the settings page.');
    }

    private function captureOutput(callable $callback): string
    {
        ob_start();
        $callback();

        return (string) ob_get_clean();
    }

    private function assertContains(string $needle, string $haystack, string $message): void
    {
        if (! str_contains($haystack, $needle)) {
            throw new RuntimeException($message . PHP_EOL . 'Missing: ' . $needle);
        }
    }
}

final class DashboardCategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @param array<int, Category> $items
     */
    public function __construct(
        public array $items = [],
    ) {
    }

    public function all(): array
    {
        return $this->items;
    }

    public function getById(string $id): ?Category
    {
        foreach ($this->items as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }

        return null;
    }

    public function save(Category $category): void
    {
        $this->items[] = $category;
    }

    public function delete(string $id): void
    {
        $this->items = array_values(array_filter(
            $this->items,
            static fn (Category $item): bool => $item->id !== $id
        ));
    }
}

final class DashboardFieldDefinitionRepository implements FieldDefinitionRepositoryInterface
{
    /**
     * @param array<int, FieldDefinition> $items
     */
    public function __construct(
        public array $items = [],
    ) {
    }

    public function all(): array
    {
        return $this->items;
    }

    public function getById(string $id): ?FieldDefinition
    {
        foreach ($this->items as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }

        return null;
    }

    public function save(FieldDefinition $definition): void
    {
        $this->items[] = $definition;
    }

    public function delete(string $id): void
    {
        $this->items = array_values(array_filter(
            $this->items,
            static fn (FieldDefinition $item): bool => $item->id !== $id
        ));
    }
}

final class DashboardEditorSettingsStorage implements EditorSettingsStorageInterface
{
    public function __construct(
        private string $editorUrl = '',
    ) {
    }

    public function getEditorUrl(): string
    {
        return $this->editorUrl;
    }

    public function saveEditorUrl(string $editorUrl): void
    {
        $this->editorUrl = $editorUrl;
    }
}
