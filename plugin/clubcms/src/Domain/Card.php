<?php

declare(strict_types=1);

namespace ClubCMS\Domain;

use DateTimeImmutable;

final class Card
{
    /**
     * @param array<string, mixed> $fields
     */
    public function __construct(
        public readonly string $id,
        public string $title,
        public string $categoryId,
        public array $fields = [],
        public CardStatus $status = CardStatus::Draft,
        public Visibility $visibility = Visibility::Public,
        public bool $isStatic = false,
        public int $position = 0,
        public ?DateTimeImmutable $publishedAt = null,
    ) {
    }

    /**
     * @return array{
     *     id: string,
     *     title: string,
     *     categoryId: string,
     *     fields: array<string, mixed>,
     *     status: string,
     *     visibility: string,
     *     isStatic: bool,
     *     position: int,
     *     publishedAt: ?string
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'categoryId' => $this->categoryId,
            'fields' => $this->fields,
            'status' => $this->status->value,
            'visibility' => $this->visibility->value,
            'isStatic' => $this->isStatic,
            'position' => $this->position,
            'publishedAt' => $this->publishedAt?->format(DATE_ATOM),
        ];
    }

    /**
     * @param array{
     *     id?: string,
     *     title?: string,
     *     categoryId?: string,
     *     fields?: array<string, mixed>,
     *     status?: string,
     *     visibility?: string,
     *     isStatic?: bool,
     *     position?: int,
     *     publishedAt?: ?string
     * } $data
     */
    public static function fromArray(array $data): self
    {
        $publishedAt = null;
        $publishedAtValue = $data['publishedAt'] ?? null;

        if (is_string($publishedAtValue) && $publishedAtValue !== '') {
            try {
                $publishedAt = new DateTimeImmutable($publishedAtValue);
            } catch (\Exception) {
                $publishedAt = null;
            }
        }

        $status = CardStatus::tryFrom((string) ($data['status'] ?? 'draft')) ?? CardStatus::Draft;
        $visibility = Visibility::tryFrom((string) ($data['visibility'] ?? 'public')) ?? Visibility::Public;

        return new self(
            (string) ($data['id'] ?? ''),
            (string) ($data['title'] ?? ''),
            (string) ($data['categoryId'] ?? ''),
            is_array($data['fields'] ?? null) ? $data['fields'] : [],
            $status,
            $visibility,
            (bool) ($data['isStatic'] ?? false),
            (int) ($data['position'] ?? 0),
            $publishedAt,
        );
    }
}
