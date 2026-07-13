<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Domain\Card;
use ClubCMS\Domain\CardStatus;
use ClubCMS\Domain\Visibility;
use DateTimeImmutable;
use DateTimeZone;
use RuntimeException;

final class CardTest
{
    public function run(): void
    {
        $this->itCreatesAnArrayRepresentation();
        $this->itBuildsFromArray();
    }

    private function itCreatesAnArrayRepresentation(): void
    {
        $card = new Card(
            'card-news',
            'Sommerlager startet',
            'cat-news',
            ['teaser' => 'Gemeinschaft und Action'],
            CardStatus::Published,
            Visibility::Members,
            true,
            3,
            new DateTimeImmutable('2026-07-10 12:00:00', new DateTimeZone('UTC'))
        );

        $this->assertSame(
            [
                'id' => 'card-news',
                'title' => 'Sommerlager startet',
                'categoryId' => 'cat-news',
                'fields' => ['teaser' => 'Gemeinschaft und Action'],
                'status' => 'published',
                'visibility' => 'members',
                'isStatic' => true,
                'position' => 3,
                'publishedAt' => '2026-07-10T12:00:00+00:00',
            ],
            $card->toArray(),
            'Card::toArray() should expose the stored data.'
        );
    }

    private function itBuildsFromArray(): void
    {
        $card = Card::fromArray([
            'id' => 'card-archive',
            'title' => 'Archiv',
            'categoryId' => 'cat-archive',
            'fields' => ['image' => 'hero.jpg'],
            'status' => 'archived',
            'visibility' => 'editorial',
            'isStatic' => false,
            'position' => 2,
            'publishedAt' => '2026-06-01T10:00:00+00:00',
        ]);

        $this->assertSame('card-archive', $card->id, 'fromArray() should map id.');
        $this->assertSame('Archiv', $card->title, 'fromArray() should map title.');
        $this->assertSame('cat-archive', $card->categoryId, 'fromArray() should map categoryId.');
        $this->assertSame(['image' => 'hero.jpg'], $card->fields, 'fromArray() should map fields.');
        $this->assertSame(CardStatus::Archived, $card->status, 'fromArray() should map status.');
        $this->assertSame(Visibility::Editorial, $card->visibility, 'fromArray() should map visibility.');
        $this->assertSame(2, $card->position, 'fromArray() should map position.');
        $this->assertSame('2026-06-01T10:00:00+00:00', $card->publishedAt?->format(DateTimeImmutable::ATOM), 'fromArray() should map publishedAt.');
    }

    private function assertSame(mixed $expected, mixed $actual, string $message): void
    {
        if ($expected !== $actual) {
            throw new RuntimeException($message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true));
        }
    }
}
