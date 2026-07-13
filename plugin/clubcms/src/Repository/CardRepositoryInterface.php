<?php

declare(strict_types=1);

namespace ClubCMS\Repository;

use ClubCMS\Domain\Card;

interface CardRepositoryInterface
{
    /**
     * @return array<int, Card>
     */
    public function all(): array;

    public function save(Card $card): void;
}
