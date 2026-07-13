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

    public function save(FieldDefinition $definition): void;
}
