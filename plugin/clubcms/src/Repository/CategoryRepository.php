<?php

declare(strict_types=1);

namespace ClubCMS\Repository;

use ClubCMS\Domain\Category;
use ClubCMS\Infrastructure\OptionStorage;

final class CategoryRepository implements CategoryRepositoryInterface
{
    private const OPTION_NAME = 'categories';

    public function __construct(
        private readonly OptionStorage $storage,
    ) {
    }

    /**
     * @return array<int, Category>
     */
    public function all(): array
    {
        $items = $this->storage->get(self::OPTION_NAME, []);

        if (! is_array($items)) {
            return [];
        }

        return array_values(array_map(
            static fn (array $item): Category => Category::fromArray($item),
            array_filter($items, 'is_array')
        ));
    }

    public function save(Category $category): void
    {
        $items = [];

        foreach ($this->all() as $existing) {
            if ($existing->id !== $category->id) {
                $items[] = $existing->toArray();
            }
        }

        $items[] = $category->toArray();

        $this->storage->update(self::OPTION_NAME, $items);
    }
}
