<?php

declare(strict_types=1);

namespace ClubCMS\Repository;

use ClubCMS\Domain\Card;
use ClubCMS\Infrastructure\OptionStorage;

final class CardRepository implements CardRepositoryInterface
{
    private const OPTION_NAME = 'cards';

    public function __construct(
        private readonly OptionStorage $storage,
    ) {
    }

    /**
     * @return array<int, Card>
     */
    public function all(): array
    {
        $items = $this->storage->get(self::OPTION_NAME, []);

        if (! is_array($items)) {
            return [];
        }

        return array_values(array_map(
            static fn (array $item): Card => Card::fromArray($item),
            array_filter($items, 'is_array')
        ));
    }

    public function save(Card $card): void
    {
        $items = [];

        foreach ($this->all() as $existing) {
            if ($existing->id !== $card->id) {
                $items[] = $existing->toArray();
            }
        }

        $items[] = $card->toArray();

        $this->storage->update(self::OPTION_NAME, $items);
    }
}
