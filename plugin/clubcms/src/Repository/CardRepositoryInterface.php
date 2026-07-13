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

    public function getById(string $id): ?Card;

    public function save(Card $card): void;

    public function delete(string $id): void;
}
