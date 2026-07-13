<?php

declare(strict_types=1);

namespace ClubCMS\Domain;

final class FieldDefinition
{
    /**
     * @param array<int, array<string, mixed>> $fields
     */
    public function __construct(
        public readonly string $id,
        public string $label,
        public array $fields = [],
    ) {
    }

    /**
     * @return array{id: string, label: string, fields: array<int, array<string, mixed>>}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'fields' => array_values($this->fields),
        ];
    }

    /**
     * @param array{id?: string, label?: string, fields?: array<int, array<string, mixed>>} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['id'] ?? ''),
            (string) ($data['label'] ?? ''),
            array_values($data['fields'] ?? []),
        );
    }
}
