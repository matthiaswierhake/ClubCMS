<?php

declare(strict_types=1);

namespace ClubCMS\Repository;

use ClubCMS\Domain\FieldDefinition;
use ClubCMS\Infrastructure\OptionStorage;

final class FieldDefinitionRepository implements FieldDefinitionRepositoryInterface
{
    private const OPTION_NAME = 'field_definitions';

    public function __construct(
        private readonly OptionStorage $storage,
    ) {
    }

    /**
     * @return array<int, FieldDefinition>
     */
    public function all(): array
    {
        $items = $this->storage->get(self::OPTION_NAME, []);

        if (! is_array($items)) {
            return [];
        }

        return array_values(array_map(
            static fn (array $item): FieldDefinition => FieldDefinition::fromArray($item),
            array_filter($items, 'is_array')
        ));
    }

    public function getById(string $id): ?FieldDefinition
    {
        foreach ($this->all() as $definition) {
            if ($definition->id === $id) {
                return $definition;
            }
        }

        return null;
    }

    public function save(FieldDefinition $definition): void
    {
        $items = [];

        foreach ($this->all() as $existing) {
            if ($existing->id !== $definition->id) {
                $items[] = $existing->toArray();
            }
        }

        $items[] = $definition->toArray();

        $this->storage->update(self::OPTION_NAME, $items);
    }

    public function delete(string $id): void
    {
        $items = [];

        foreach ($this->all() as $existing) {
            if ($existing->id !== $id) {
                $items[] = $existing->toArray();
            }
        }

        $this->storage->update(self::OPTION_NAME, $items);
    }
}
