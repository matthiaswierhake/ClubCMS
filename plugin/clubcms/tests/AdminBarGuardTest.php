<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Security\AdminBarGuard;
use RuntimeException;

final class AdminBarGuardTest
{
    public function run(): void
    {
        $this->itHidesTheAdminBarForNonAdmins();
        $this->itKeepsTheAdminBarForAdmins();
    }

    private function itHidesTheAdminBarForNonAdmins(): void
    {
        $guard = new AdminBarGuard(
            static fn (string $capability): bool => false
        );

        $this->assertFalse($guard->filter(true), 'Non-admins should not see the admin bar.');
    }

    private function itKeepsTheAdminBarForAdmins(): void
    {
        $guard = new AdminBarGuard(
            static fn (string $capability): bool => true
        );

        $this->assertTrue($guard->filter(true), 'Admins may keep the admin bar.');
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
