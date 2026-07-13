<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Domain\Category;
use RuntimeException;

final class CategoryTest
{
    public function run(): void
    {
        $this->itCreatesAnArrayRepresentation();
        $this->itNormalizesFieldDefinitionIds();
        $this->itBuildsFromArray();
        $this->itFallsBackToDefaultsWhenDataIsMissing();
    }

    private function itCreatesAnArrayRepresentation(): void
    {
        $category = new Category(
            'cat-news',
            'News',
            'news',
            'position_desc',
            ['fd-hero', 'fd-content']
        );

        $this->assertSame(
            [
                'id' => 'cat-news',
                'label' => 'News',
                'slug' => 'news',
                'sortMode' => 'position_desc',
                'fieldDefinitionIds' => ['fd-hero', 'fd-content'],
            ],
            $category->toArray(),
            'toArray() should expose the stored data.'
        );
    }

    private function itNormalizesFieldDefinitionIds(): void
    {
        $category = new Category(
            'cat-events',
            'Events',
            'events',
            'date_asc',
            [
                4 => 'fd-primary',
                9 => 'fd-secondary',
            ]
        );

        $this->assertSame(
            [
                'id' => 'cat-events',
                'label' => 'Events',
                'slug' => 'events',
                'sortMode' => 'date_asc',
                'fieldDefinitionIds' => ['fd-primary', 'fd-secondary'],
            ],
            $category->toArray(),
            'toArray() should reindex fieldDefinitionIds.'
        );
    }

    private function itBuildsFromArray(): void
    {
        $category = Category::fromArray([
            'id' => 'cat-members',
            'label' => 'Members',
            'slug' => 'members',
            'sortMode' => 'title_desc',
            'fieldDefinitionIds' => [
                2 => 'fd-profile',
                8 => 'fd-access',
            ],
        ]);

        $this->assertSame('cat-members', $category->id, 'fromArray() should map id.');
        $this->assertSame('Members', $category->label, 'fromArray() should map label.');
        $this->assertSame('members', $category->slug, 'fromArray() should map slug.');
        $this->assertSame('title_desc', $category->sortMode, 'fromArray() should map sort mode.');
        $this->assertSame(
            ['fd-profile', 'fd-access'],
            $category->fieldDefinitionIds,
            'fromArray() should normalize field definition ids.'
        );

        $legacyCategory = Category::fromArray([
            'sortMode' => 'manual',
        ]);

        $this->assertSame('position_asc', $legacyCategory->sortMode, 'Legacy manual sort mode should normalize to position_asc.');
    }

    private function itFallsBackToDefaultsWhenDataIsMissing(): void
    {
        $category = Category::fromArray([]);

        $this->assertSame('', $category->id, 'Missing id should default to empty string.');
        $this->assertSame('', $category->label, 'Missing label should default to empty string.');
        $this->assertSame('', $category->slug, 'Missing slug should default to empty string.');
        $this->assertSame('date_desc', $category->sortMode, 'Missing sort mode should default to date_desc.');
        $this->assertSame([], $category->fieldDefinitionIds, 'Missing fieldDefinitionIds should default to an empty array.');
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    private function assertSame(mixed $expected, mixed $actual, string $message): void
    {
        if ($expected !== $actual) {
            throw new RuntimeException($message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true));
        }
    }
}
