<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Security\AdminBarGuard;
use RuntimeException;

final class AdminBarGuardTest
{
    public function run(): void
    {
        $guard = new AdminBarGuard();
        $this->assertFalse($guard->filter(true), 'The admin bar should always be hidden.');
    }

    private function assertFalse(bool $condition, string $message): void
    {
        if ($condition) {
            throw new RuntimeException($message);
        }
    }
}
