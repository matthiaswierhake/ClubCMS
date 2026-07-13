<?php

declare(strict_types=1);

namespace ClubCMS\Repository;

use ClubCMS\Domain\Category;

interface CategoryRepositoryInterface
{
    /**
     * @return array<int, Category>
     */
    public function all(): array;

    public function getById(string $id): ?Category;

    public function save(Category $category): void;

    public function delete(string $id): void;
}
