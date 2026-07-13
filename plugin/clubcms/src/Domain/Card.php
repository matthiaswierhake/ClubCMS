<?php

declare(strict_types=1);

namespace ClubCMS\Domain;

use DateTimeImmutable;

final class Card
{
    /**
     * @param array<string, mixed> $fields
     */
    public function __construct(
        public readonly string $id,
        public string $title,
        public string $categoryId,
        public array $fields = [],
        public CardStatus $status = CardStatus::Draft,
        public Visibility $visibility = Visibility::Public,
        public bool $isStatic = false,
        public int $position = 0,
        public ?DateTimeImmutable $publishedAt = null,
    ) {
    }
}
