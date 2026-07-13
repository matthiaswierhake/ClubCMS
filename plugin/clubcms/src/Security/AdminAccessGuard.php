<?php

declare(strict_types=1);

namespace ClubCMS\Security;

final class AdminAccessGuard
{
    private AccessRoleModel $roles;

    /** @var callable(string): void|null */
    private $redirect;

    /** @var callable(): string|null */
    private $requestUri;

    /** @var callable(): string|null */
    private $homeUrl;

    /** @var callable(): void|null */
    private $terminate;

    public function __construct(
        $canManageOptions = null,
        $redirect = null,
        $requestUri = null,
        $homeUrl = null,
        $terminate = null,
    ) {
        $this->roles = new AccessRoleModel($canManageOptions);
        $this->redirect = $redirect;
        $this->requestUri = $requestUri;
        $this->homeUrl = $homeUrl;
        $this->terminate = $terminate;
    }

    public function enforce(): void
    {
        if ($this->shouldBypass()) {
            return;
        }

        if ($this->roles->isAdmin()) {
            return;
        }

        $redirect = $this->redirect ?? static function (string $url): void {
            if (function_exists('wp_safe_redirect')) {
                wp_safe_redirect($url);
            }
        };

        $redirect($this->homeUrl());

        $terminate = $this->terminate ?? static function (): void {
            exit;
        };

        $terminate();
    }

    private function shouldBypass(): bool
    {
        $uri = $this->requestUri();

        return str_contains($uri, 'admin-ajax.php') || str_contains($uri, 'admin-post.php');
    }

    private function requestUri(): string
    {
        if ($this->requestUri !== null) {
            return (string) ($this->requestUri)();
        }

        return (string) ($_SERVER['REQUEST_URI'] ?? '');
    }

    private function homeUrl(): string
    {
        if ($this->homeUrl !== null) {
            return (string) ($this->homeUrl)();
        }

        if (function_exists('home_url')) {
            return (string) home_url('/');
        }

        return '/';
    }
}
