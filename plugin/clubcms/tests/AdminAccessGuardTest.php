<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Security\AdminAccessGuard;
use RuntimeException;

final class AdminAccessGuardTest
{
    public function run(): void
    {
        $this->itRedirectsNonAdminsOutOfWpAdmin();
        $this->itAllowsAdminsToStayInWpAdmin();
        $this->itBypassesAjaxRequests();
    }

    private function itRedirectsNonAdminsOutOfWpAdmin(): void
    {
        $redirectTarget = null;
        $terminated = false;

        $guard = new AdminAccessGuard(
            canManageOptions: static fn (string $capability): bool => false,
            redirect: static function (string $url) use (&$redirectTarget): void {
                $redirectTarget = $url;
            },
            requestUri: static fn (): string => '/wp-admin/admin.php?page=clubcms',
            homeUrl: static fn (): string => 'https://example.test/',
            terminate: static function () use (&$terminated): void {
                $terminated = true;
            }
        );

        $guard->enforce();

        $this->assertSame('https://example.test/', $redirectTarget, 'Non-admins should be redirected to the front page.');
        $this->assertTrue($terminated, 'Non-admin access should terminate after redirect.');
    }

    private function itAllowsAdminsToStayInWpAdmin(): void
    {
        $redirectTarget = null;
        $terminated = false;

        $guard = new AdminAccessGuard(
            canManageOptions: static fn (string $capability): bool => true,
            redirect: static function (string $url) use (&$redirectTarget): void {
                $redirectTarget = $url;
            },
            requestUri: static fn (): string => '/wp-admin/admin.php?page=clubcms',
            homeUrl: static fn (): string => 'https://example.test/',
            terminate: static function () use (&$terminated): void {
                $terminated = true;
            }
        );

        $guard->enforce();

        $this->assertSame(null, $redirectTarget, 'Admins should not be redirected.');
        $this->assertFalse($terminated, 'Admins should not be terminated.');
    }

    private function itBypassesAjaxRequests(): void
    {
        $redirectTarget = null;
        $terminated = false;

        $guard = new AdminAccessGuard(
            canManageOptions: static fn (string $capability): bool => false,
            redirect: static function (string $url) use (&$redirectTarget): void {
                $redirectTarget = $url;
            },
            requestUri: static fn (): string => '/wp-admin/admin-ajax.php',
            homeUrl: static fn (): string => 'https://example.test/',
            terminate: static function () use (&$terminated): void {
                $terminated = true;
            }
        );

        $guard->enforce();

        $this->assertSame(null, $redirectTarget, 'Ajax requests should not be redirected.');
        $this->assertFalse($terminated, 'Ajax requests should not be terminated.');
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

    private function assertSame(mixed $expected, mixed $actual, string $message): void
    {
        if ($expected !== $actual) {
            throw new RuntimeException($message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true));
        }
    }
}
