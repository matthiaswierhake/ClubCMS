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

    public function save(Category $category): void;
}
