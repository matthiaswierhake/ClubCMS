<?php

declare(strict_types=1);

namespace ClubCMS\Frontend;

use ClubCMS\Repository\CategoryRepositoryInterface;
use ClubCMS\Repository\CardRepositoryInterface;
use ClubCMS\Rendering\LandingPageRenderer;

final class LandingPageShortcode
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly CardRepositoryInterface $cardRepository,
        private readonly LandingPageRenderer $renderer = new LandingPageRenderer(),
    ) {
    }

    public function register(): void
    {
        add_shortcode('clubcms_landing_page', [$this, 'render']);
        add_shortcode('clubcms_column', [$this, 'renderColumn']);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function render(array $attributes = []): string
    {
        $showEditorControls = function_exists('is_user_logged_in') && is_user_logged_in();
        $categories = $this->resolveCategoriesForColumns($attributes);

        return $this->renderer->render(
            $categories,
            $this->cardRepository->all(),
            $showEditorControls
        );
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function renderColumn(array $attributes = []): string
    {
        $showEditorControls = function_exists('is_user_logged_in') && is_user_logged_in();
        $category = $this->resolveSingleCategory($attributes);

        return $this->renderer->renderColumn(
            $category,
            $this->cardRepository->all(),
            $showEditorControls
        );
    }

    /**
     * @param array<string, mixed> $attributes
     * @return array<int, \ClubCMS\Domain\Category|null>
     */
    private function resolveCategoriesForColumns(array $attributes): array
    {
        $categories = $this->categoryRepository->all();
        $byId = [];
        $bySlug = [];

        foreach ($categories as $category) {
            $byId[$category->id] = $category;
            $bySlug[$category->slug] = $category;
        }

        $used = [];
        $resolved = [];

        for ($index = 1; $index <= 4; $index++) {
            $attributeValue = $this->extractColumnAttribute($attributes, $index);

            if ($attributeValue !== null && $attributeValue !== '') {
                $category = $this->findCategory($attributeValue, $byId, $bySlug);

                if ($category !== null && ! isset($used[$category->id])) {
                    $resolved[] = $category;
                    $used[$category->id] = true;
                    continue;
                }

                $resolved[] = null;
                continue;
            }

            $resolved[] = $this->nextUnusedCategory($categories, $used);

            $lastIndex = array_key_last($resolved);
            if ($lastIndex !== null && $resolved[$lastIndex] !== null) {
                $used[$resolved[$lastIndex]->id] = true;
            }
        }

        return $resolved;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function extractColumnAttribute(array $attributes, int $index): ?string
    {
        foreach ([
            "spalte_{$index}",
            "spalte{$index}",
            "column_{$index}",
            "column{$index}",
            "col_{$index}",
            "col{$index}",
        ] as $key) {
            if (! array_key_exists($key, $attributes)) {
                continue;
            }

            $value = $attributes[$key];

            if (is_string($value)) {
                return trim($value);
            }
        }

        return null;
    }

    /**
     * @param array<string, \ClubCMS\Domain\Category> $byId
     * @param array<string, \ClubCMS\Domain\Category> $bySlug
     */
    private function findCategory(string $value, array $byId, array $bySlug): ?\ClubCMS\Domain\Category
    {
        $normalized = $this->normalizeLookupValue($value);

        return $byId[$normalized] ?? $bySlug[$normalized] ?? null;
    }

    /**
     * @param array<int, \ClubCMS\Domain\Category> $categories
     * @param array<string, bool> $used
     */
    private function nextUnusedCategory(array $categories, array $used): ?\ClubCMS\Domain\Category
    {
        foreach ($categories as $category) {
            if (! isset($used[$category->id])) {
                return $category;
            }
        }

        return null;
    }

    private function normalizeLookupValue(string $value): string
    {
        if (function_exists('sanitize_key')) {
            return (string) sanitize_key($value);
        }

        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9_-]/', '', $value) ?? '';

        return trim($value, "_-");
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function resolveSingleCategory(array $attributes): ?\ClubCMS\Domain\Category
    {
        $lookup = '';

        foreach (['thema', 'kategorie', 'category', 'slug', 'id'] as $key) {
            if (! array_key_exists($key, $attributes)) {
                continue;
            }

            $value = $attributes[$key];

            if (is_string($value) && trim($value) !== '') {
                $lookup = trim($value);
                break;
            }
        }

        if ($lookup === '') {
            return null;
        }

        $categories = $this->categoryRepository->all();

        foreach ($categories as $category) {
            if ($this->normalizeLookupValue($category->id) === $this->normalizeLookupValue($lookup)) {
                return $category;
            }

            if ($this->normalizeLookupValue($category->slug) === $this->normalizeLookupValue($lookup)) {
                return $category;
            }
        }

        return null;
    }
}
