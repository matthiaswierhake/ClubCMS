<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Domain\Category;
use ClubCMS\Repository\CategoryRepositoryInterface;
use ClubCMS\Repository\CardRepositoryInterface;
use ClubCMS\Frontend\LandingPageShortcode;
use RuntimeException;

final class LandingPageShortcodeTest
{
    public function run(): void
    {
        $this->itMapsCategoriesToExplicitColumns();
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
}

final class EmptyCardRepository implements CardRepositoryInterface
{
    public function all(): array
    {
        return [];
    }

    public function save(\ClubCMS\Domain\Card $card): void
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
