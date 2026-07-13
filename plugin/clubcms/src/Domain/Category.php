<?php

declare(strict_types=1);

namespace ClubCMS\Domain;

final class Category
{
    private const SORT_MODE_LABELS = [
        'date_desc' => 'Datum absteigend',
        'date_asc' => 'Datum aufsteigend',
        'position_asc' => 'Position aufsteigend',
        'position_desc' => 'Position absteigend',
        'title_asc' => 'Titel aufsteigend',
        'title_desc' => 'Titel absteigend',
    ];

    /**
     * @param array<int, string> $fieldDefinitionIds
     */
    public function __construct(
        public readonly string $id,
        public string $label,
        public string $slug,
        public string $sortMode = 'date_desc',
        public array $fieldDefinitionIds = [],
    ) {
        $this->sortMode = self::normalizeSortMode($this->sortMode);
    }

    /**
     * @return array<string, string>
     */
    public static function sortModeOptions(): array
    {
        return self::SORT_MODE_LABELS;
    }

    public static function normalizeSortMode(string $sortMode): string
    {
        $sortMode = strtolower(trim($sortMode));

        return match ($sortMode) {
            'date', 'date_desc' => 'date_desc',
            'date_asc' => 'date_asc',
            'manual', 'position', 'position_asc' => 'position_asc',
            'position_desc' => 'position_desc',
            'title', 'title_asc' => 'title_asc',
            'title_desc' => 'title_desc',
            default => 'date_desc',
        };
    }

    public static function sortModeLabel(string $sortMode): string
    {
        $normalized = self::normalizeSortMode($sortMode);

        return self::SORT_MODE_LABELS[$normalized] ?? $normalized;
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
            'sortMode' => self::normalizeSortMode($this->sortMode),
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
            self::normalizeSortMode((string) ($data['sortMode'] ?? 'date_desc')),
            array_values($data['fieldDefinitionIds'] ?? []),
        );
    }
}
