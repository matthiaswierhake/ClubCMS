<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Security\EditorAccessGuard;
use RuntimeException;

final class EditorAccessGuardTest
{
    public function run(): void
    {
        $this->itAllowsEditors();
        $this->itAllowsAdmins();
        $this->itRejectsVisitors();
    }

    private function itAllowsEditors(): void
    {
        $guard = new EditorAccessGuard(
            static fn (string $capability): bool => $capability === 'edit_posts'
        );

        $this->assertTrue($guard->canAccess(), 'Editors should be allowed.');
    }

    private function itAllowsAdmins(): void
    {
        $guard = new EditorAccessGuard(
            static fn (string $capability): bool => $capability === 'manage_options'
        );

        $this->assertTrue($guard->canAccess(), 'Admins should be allowed.');
    }

    private function itRejectsVisitors(): void
    {
        $guard = new EditorAccessGuard(
            static fn (string $capability): bool => false
        );

        $this->assertFalse($guard->canAccess(), 'Visitors should not be allowed.');
    }

    private function assertTrue(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new RuntimeException($message);
        }
    }

    private function assertFalse(bool $condition, string $message): void
    {
        if ($condition) {
            throw new RuntimeException($message);
        }
    }
}
