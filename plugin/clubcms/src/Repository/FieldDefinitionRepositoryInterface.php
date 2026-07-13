<?php

declare(strict_types=1);

namespace ClubCMS\Repository;

use ClubCMS\Domain\FieldDefinition;

interface FieldDefinitionRepositoryInterface
{
    /**
     * @return array<int, FieldDefinition>
     */
    public function all(): array;

    public function getById(string $id): ?FieldDefinition;

    public function save(FieldDefinition $definition): void;

    public function delete(string $id): void;
}
