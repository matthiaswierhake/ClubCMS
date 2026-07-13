<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Domain\Category;
use ClubCMS\Frontend\LandingPageShortcode;
use ClubCMS\Repository\CategoryRepositoryInterface;
use ClubCMS\Repository\CardRepositoryInterface;
use RuntimeException;

final class LandingPageColumnShortcodeTest
{
    public function run(): void
    {
        $this->itRendersASingleCategoryColumn();
    }

    private function itRendersASingleCategoryColumn(): void
    {
        $shortcode = new LandingPageShortcode(
            new ColumnCategoryRepository([
                new Category('cat-news', 'News', 'news', 'date'),
                new Category('cat-events', 'Termine', 'termine', 'date'),
            ]),
            new EmptyColumnCardRepository()
        );

        $html = $shortcode->renderColumn([
            'thema' => 'cat-events',
        ]);

        if (! str_contains($html, 'clubcms-columns--single')) {
            throw new RuntimeException('The single column wrapper should be rendered.');
        }

        if (! str_contains($html, '>Termine<')) {
            throw new RuntimeException('The requested category should be rendered.');
        }

        if (str_contains($html, '>News<')) {
            throw new RuntimeException('Only the selected category should be rendered.');
        }
    }
}

final class EmptyColumnCardRepository implements CardRepositoryInterface
{
    public function all(): array
    {
        return [];
    }

    public function save(\ClubCMS\Domain\Card $card): void
    {
    }
}

final class ColumnCategoryRepository implements CategoryRepositoryInterface
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
