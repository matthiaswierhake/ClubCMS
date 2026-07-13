<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Domain\Category;
use ClubCMS\Infrastructure\EditorSettingsStorageInterface;
use ClubCMS\Repository\CategoryRepositoryInterface;
use ClubCMS\Repository\CardRepositoryInterface;
use ClubCMS\Frontend\LandingPageShortcode;
use RuntimeException;

final class LandingPageShortcodeTest
{
    public function run(): void
    {
        $this->itMapsCategoriesToExplicitColumns();
        $this->itUsesTheConfiguredEditorUrl();
    }

    private function itMapsCategoriesToExplicitColumns(): void
    {
        $shortcode = new LandingPageShortcode(
            new ShortcodeCategoryRepository([
                new Category('cat-news', 'News', 'news', 'date'),
                new Category('cat-events', 'Termine', 'termine', 'date'),
            ]),
            new EmptyCardRepository()
        );

        $html = $shortcode->render([
            'spalte_1' => 'cat-events',
            'spalte_2' => 'cat-news',
        ]);

        $eventsPosition = strpos($html, '>Termine<');
        $newsPosition = strpos($html, '>News<');

        if ($eventsPosition === false || $newsPosition === false) {
            throw new RuntimeException('Expected both category headings to be rendered.');
        }

        if ($eventsPosition >= $newsPosition) {
            throw new RuntimeException('The explicit shortcode mapping should place Termine before News.');
        }
    }

    private function itUsesTheConfiguredEditorUrl(): void
    {
        $shortcode = new LandingPageShortcode(
            new ShortcodeCategoryRepository([
                new Category('cat-news', 'News', 'news', 'date'),
            ]),
            new EmptyCardRepository(),
            editorSettingsStorage: new ShortcodeEditorSettingsStorage('/clubcms-editor/')
        );

        $previousLoginState = $GLOBALS['clubcms_is_user_logged_in'] ?? null;

        try {
            $GLOBALS['clubcms_is_user_logged_in'] = true;

            $html = $shortcode->render([
                'spalte_1' => 'cat-news',
            ]);
        } finally {
            if ($previousLoginState === null) {
                unset($GLOBALS['clubcms_is_user_logged_in']);
            } else {
                $GLOBALS['clubcms_is_user_logged_in'] = $previousLoginState;
            }
        }

        $this->assertContains('/clubcms-editor/?category_id=cat-news', $html, 'Configured editor URL should be used for new-card actions.');
    }

    private function assertContains(string $needle, string $haystack, string $message): void
    {
        if (! str_contains($haystack, $needle)) {
            throw new RuntimeException($message . PHP_EOL . 'Missing: ' . $needle);
        }
    }
}

final class EmptyCardRepository implements CardRepositoryInterface
{
    public function all(): array
    {
        return [];
    }

    public function getById(string $id): ?\ClubCMS\Domain\Card
    {
        return null;
    }

    public function save(\ClubCMS\Domain\Card $card): void
    {
    }

    public function delete(string $id): void
    {
    }
}

final class ShortcodeCategoryRepository implements CategoryRepositoryInterface
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

final class ShortcodeEditorSettingsStorage implements EditorSettingsStorageInterface
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
