<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Admin\CardSubmissionHandler;
use ClubCMS\Domain\Card;
use ClubCMS\Domain\CardStatus;
use ClubCMS\Domain\Visibility;
use ClubCMS\Repository\CardRepositoryInterface;
use RuntimeException;

final class CardSubmissionHandlerTest
{
    public function run(): void
    {
        $this->itSavesAValidCard();
        $this->itDeletesAValidCard();
        $this->itRejectsInvalidJson();
    }

    private function itSavesAValidCard(): void
    {
        $repository = new InMemoryCardRepository();
        $handler = new CardSubmissionHandler($repository);

        $saved = $handler->handleCard([
            'id' => 'card-news',
            'title' => 'Sommerlager',
            'category_id' => 'cat-news',
            'fields_json' => '{"teaser":"Action"}',
            'status' => 'published',
            'visibility' => 'members',
            'position' => '3',
            'published_at' => '2026-07-10 12:00:00',
            'is_static' => '1',
        ]);

        $this->assertTrue($saved, 'Valid card data should be accepted.');
        $this->assertCount(1, $repository->items, 'Card should be stored.');
        $this->assertSame('card-news', $repository->items[0]->id, 'Stored card should use the normalized id.');
        $this->assertSame('Sommerlager', $repository->items[0]->title, 'Stored card should use the title.');
        $this->assertSame('cat-news', $repository->items[0]->categoryId, 'Stored card should use the category id.');
        $this->assertSame(CardStatus::Published, $repository->items[0]->status, 'Stored card should use the status.');
        $this->assertSame(Visibility::Members, $repository->items[0]->visibility, 'Stored card should use the visibility.');
        $this->assertTrue($repository->items[0]->isStatic, 'Stored card should use the static flag.');
        $this->assertSame(3, $repository->items[0]->position, 'Stored card should use the position.');
        $this->assertSame(['teaser' => 'Action'], $repository->items[0]->fields, 'Stored card should decode fields JSON.');
    }

    private function itDeletesAValidCard(): void
    {
        $repository = new InMemoryCardRepository([
            new Card('card-news', 'Sommerlager', 'cat-news'),
        ]);
        $handler = new CardSubmissionHandler($repository);

        $deleted = $handler->handleDelete([
            'id' => 'card-news',
        ]);

        $this->assertTrue($deleted, 'Valid card delete should be accepted.');
        $this->assertCount(0, $repository->items, 'Card should be removed.');
    }

    private function itRejectsInvalidJson(): void
    {
        $repository = new InMemoryCardRepository();
        $handler = new CardSubmissionHandler($repository);

        $saved = $handler->handleCard([
            'id' => 'card-bad',
            'title' => 'Bad',
            'category_id' => 'cat-news',
            'fields_json' => '{broken',
        ]);

        $this->assertFalse($saved, 'Invalid JSON should be rejected.');
        $this->assertCount(0, $repository->items, 'Invalid JSON must not be stored.');
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

final class InMemoryCardRepository implements CardRepositoryInterface
{
    /**
     * @param array<int, Card> $items
     */
    public function __construct(
        public array $items = [],
    ) {
    }

    public function all(): array
    {
        return $this->items;
    }

    public function getById(string $id): ?Card
    {
        foreach ($this->items as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }

        return null;
    }

    public function save(Card $card): void
    {
        $this->delete($card->id);
        $this->items[] = $card;
    }

    public function delete(string $id): void
    {
        $this->items = array_values(array_filter(
            $this->items,
            static fn (Card $item): bool => $item->id !== $id
        ));
    }
}
