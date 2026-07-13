<?php

declare(strict_types=1);

namespace ClubCMS\Infrastructure;

final class EditorSettingsStorage implements EditorSettingsStorageInterface
{
    private const OPTION_NAME = 'editor_url';

    public function __construct(
        private readonly OptionStorage $storage = new OptionStorage(),
    ) {
    }

    public function getEditorUrl(): string
    {
        return trim((string) $this->storage->get(self::OPTION_NAME, ''));
    }

    public function saveEditorUrl(string $editorUrl): void
    {
        $this->storage->update(self::OPTION_NAME, trim($editorUrl));
    }
}
