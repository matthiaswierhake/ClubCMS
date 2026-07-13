<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Admin\SettingsSubmissionHandler;
use ClubCMS\Domain\Category;
use ClubCMS\Domain\FieldDefinition;
use ClubCMS\Repository\CategoryRepositoryInterface;
use ClubCMS\Repository\FieldDefinitionRepositoryInterface;
use RuntimeException;

final class SettingsSubmissionHandlerTest
{
    public function run(): void
    {
        $this->itSavesAValidFieldDefinition();
        $this->itRejectsInvalidJson();
        $this->itSavesAValidCategory();
        $this->itRejectsIncompleteInput();
    }

    private function itSavesAValidFieldDefinition(): void
    {
        $categoryRepository = new InMemoryCategoryRepository();
        $fieldDefinitionRepository = new InMemoryFieldDefinitionRepository();
        $handler = new SettingsSubmissionHandler($categoryRepository, $fieldDefinitionRepository);

        $saved = $handler->handleFieldDefinition([
            'id' => 'fd-news',
            'label' => 'News',
            'fields_json' => '[{"name":"headline","type":"text"}]',
        ]);

        $this->assertTrue($saved, 'Valid field definition data should be accepted.');
        $this->assertCount(1, $fieldDefinitionRepository->items, 'Field definition should be stored.');
        $this->assertSame('fd-news', $fieldDefinitionRepository->items[0]->id, 'Stored field definition should use the normalized id.');
        $this->assertSame('News', $fieldDefinitionRepository->items[0]->label, 'Stored field definition should use the label.');
        $this->assertSame(
            [['name' => 'headline', 'type' => 'text']],
            $fieldDefinitionRepository->items[0]->fields,
            'Stored field definition should preserve decoded JSON fields.'
        );
    }

    private function itSavesAValidCategory(): void
    {
        $categoryRepository = new InMemoryCategoryRepository();
        $fieldDefinitionRepository = new InMemoryFieldDefinitionRepository();
        $handler = new SettingsSubmissionHandler($categoryRepository, $fieldDefinitionRepository);

        $saved = $handler->handleCategory([
            'id' => 'cat-news',
            'label' => 'News',
            'slug' => 'News Items',
            'sort_mode' => 'manual',
            'field_definition_ids' => 'fd-hero, fd-content',
        ]);

        $this->assertTrue($saved, 'Valid category data should be accepted.');
        $this->assertCount(1, $categoryRepository->items, 'Category should be stored.');
        $this->assertSame('cat-news', $categoryRepository->items[0]->id, 'Stored category should use the normalized id.');
        $this->assertSame('news-items', $categoryRepository->items[0]->slug, 'Stored category should normalize the slug.');
        $this->assertSame('manual', $categoryRepository->items[0]->sortMode, 'Stored category should keep sort mode.');
        $this->assertSame(['fd-hero', 'fd-content'], $categoryRepository->items[0]->fieldDefinitionIds, 'Stored category should normalize the field definition ids.');
    }

    private function itRejectsInvalidJson(): void
    {
        $categoryRepository = new InMemoryCategoryRepository();
        $fieldDefinitionRepository = new InMemoryFieldDefinitionRepository();
        $handler = new SettingsSubmissionHandler($categoryRepository, $fieldDefinitionRepository);

        $saved = $handler->handleFieldDefinition([
            'id' => 'fd-invalid',
            'label' => 'Invalid',
            'fields_json' => '{broken json',
        ]);

        $this->assertFalse($saved, 'Invalid JSON should be rejected.');
        $this->assertCount(0, $fieldDefinitionRepository->items, 'Invalid JSON must not be stored.');
        $this->assertSame('Die Felddefinition enthält ungültiges JSON.', $handler->getLastError(), 'Invalid JSON should set a helpful error message.');
    }

    private function itRejectsIncompleteInput(): void
    {
        $categoryRepository = new InMemoryCategoryRepository();
        $fieldDefinitionRepository = new InMemoryFieldDefinitionRepository();
        $handler = new SettingsSubmissionHandler($categoryRepository, $fieldDefinitionRepository);

        $saved = $handler->handleFieldDefinition([
            'id' => '',
            'label' => 'Missing Id',
            'fields_json' => '[]',
        ]);

        $this->assertFalse($saved, 'Incomplete field definition data should be rejected.');
        $this->assertCount(0, $fieldDefinitionRepository->items, 'Rejected field definition must not be stored.');
    }

    private function assertTrue(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new RuntimeException($message);
        }
    }

    private function assertFalse(bool $condition, string $message): void
    {
        if ($condition) {
            throw new RuntimeException($message);
        }
    }

    private function assertSame(mixed $expected, mixed $actual, string $message): void
    {
        if ($expected !== $actual) {
            throw new RuntimeException($message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true));
        }
    }

    private function assertCount(int $expected, array $actual, string $message): void
    {
        if (count($actual) !== $expected) {
            throw new RuntimeException($message . PHP_EOL . 'Expected: ' . $expected . PHP_EOL . 'Actual:   ' . count($actual));
        }
    }
}

final class InMemoryCategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @var array<int, Category>
     */
    public array $items = [];

    public function all(): array
    {
        return $this->items;
    }

    public function save(Category $category): void
    {
        $this->items[] = $category;
    }
}

final class InMemoryFieldDefinitionRepository implements FieldDefinitionRepositoryInterface
{
    /**
     * @var array<int, FieldDefinition>
     */
    public array $items = [];

    public function all(): array
    {
        return $this->items;
    }

    public function save(FieldDefinition $definition): void
    {
        $this->items[] = $definition;
    }
}
