<?php

declare(strict_types=1);

namespace ClubCMS\Domain;

final class Category
{
    /**
     * @param array<int, string> $fieldDefinitionIds
     */
    public function __construct(
        public readonly string $id,
        public string $label,
        public string $slug,
        public string $sortMode = 'date',
        public array $fieldDefinitionIds = [],
    ) {
    }

    /**
     * @return array{id: string, label: string, slug: string, sortMode: string, fieldDefinitionIds: array<int, string>}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'slug' => $this->slug,
            'sortMode' => $this->sortMode,
            'fieldDefinitionIds' => array_values($this->fieldDefinitionIds),
        ];
    }

    /**
     * @param array{id?: string, label?: string, slug?: string, sortMode?: string, fieldDefinitionIds?: array<int, string>} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['id'] ?? ''),
            (string) ($data['label'] ?? ''),
            (string) ($data['slug'] ?? ''),
            (string) ($data['sortMode'] ?? 'date'),
            array_values($data['fieldDefinitionIds'] ?? []),
        );
    }
}
