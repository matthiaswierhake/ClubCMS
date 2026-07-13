<?php

declare(strict_types=1);

namespace ClubCMS\Admin;

use ClubCMS\Domain\Card;
use ClubCMS\Domain\CardStatus;
use ClubCMS\Domain\Visibility;
use ClubCMS\Repository\CardRepositoryInterface;

final class CardSubmissionHandler
{
    private ?string $lastError = null;

    public function __construct(
        private readonly CardRepositoryInterface $cardRepository,
    ) {
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * @param array<string, mixed> $post
     */
    public function handleCard(array $post): bool
    {
        $this->lastError = null;

        $id = $this->sanitizeKey((string) ($post['id'] ?? ''));
        $originalId = $this->sanitizeKey((string) ($post['original_id'] ?? $id));
        $title = $this->sanitizeText((string) ($post['title'] ?? ''));
        $categoryId = $this->sanitizeKey((string) ($post['category_id'] ?? ''));
        $fieldsJson = (string) ($post['fields_json'] ?? '{}');
        $status = $this->normalizeCardStatus((string) ($post['status'] ?? 'draft'));
        $visibility = $this->normalizeVisibility((string) ($post['visibility'] ?? 'public'));
        $isStatic = ! empty($post['is_static']);
        $position = (int) ($post['position'] ?? 0);
        $publishedAtInput = (string) ($post['published_at'] ?? '');
        $publishedAt = $this->parseDateTime($publishedAtInput);

        if ($id === '' || $title === '' || $categoryId === '') {
            $this->lastError = 'ID, Titel und Kategorie sind erforderlich.';

            return false;
        }

        try {
            $fields = json_decode($fieldsJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            $this->lastError = 'Die Card-Felder enthalten ungültiges JSON.';

            return false;
        }

        if (! is_array($fields)) {
            $this->lastError = 'Die Card-Felder müssen als JSON-Objekt oder JSON-Array angegeben werden.';

            return false;
        }

        if (trim($publishedAtInput) !== '' && $publishedAt === null) {
            return false;
        }

        if ($originalId !== '' && $originalId !== $id) {
            $this->cardRepository->delete($originalId);
        }

        $this->cardRepository->save(
            new Card(
                $id,
                $title,
                $categoryId,
                $fields,
                $status,
                $visibility,
                $isStatic,
                $position,
                $publishedAt
            )
        );

        return true;
    }

    /**
     * @param array<string, mixed> $post
     */
    public function handleDelete(array $post): bool
    {
        $this->lastError = null;

        $id = $this->sanitizeKey((string) ($post['id'] ?? ''));

        if ($id === '') {
            return false;
        }

        $this->cardRepository->delete($id);

        return true;
    }

    private function normalizeCardStatus(string $value): CardStatus
    {
        return CardStatus::tryFrom($this->sanitizeKey($value)) ?? CardStatus::Draft;
    }

    private function normalizeVisibility(string $value): Visibility
    {
        return Visibility::tryFrom($this->sanitizeKey($value)) ?? Visibility::Public;
    }

    private function parseDateTime(string $value): ?\DateTimeImmutable
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception) {
            $this->lastError = 'Das Veröffentlichungsdatum ist ungültig.';

            return null;
        }
    }

    private function sanitizeKey(string $value): string
    {
        if (function_exists('sanitize_key')) {
            return (string) sanitize_key($value);
        }

        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9_-]/', '', $value) ?? '';

        return trim($value, "_-");
    }

    private function sanitizeText(string $value): string
    {
        if (function_exists('sanitize_text_field')) {
            return (string) sanitize_text_field($value);
        }

        $value = strip_tags($value);
        $value = preg_replace('/[\r\n\t ]+/', ' ', $value) ?? $value;

        return trim($value);
    }
}
