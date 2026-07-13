<?php

declare(strict_types=1);

namespace ClubCMS\Infrastructure;

interface EditorSettingsStorageInterface
{
    public function getEditorUrl(): string;

    public function saveEditorUrl(string $editorUrl): void;
}
