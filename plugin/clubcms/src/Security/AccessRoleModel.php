<?php

declare(strict_types=1);

namespace ClubCMS\Security;

use ClubCMS\Domain\Visibility;

final class AccessRoleModel
{
    /** @var callable(string): bool|null */
    private $canCurrentUser;

    /** @var callable(): bool|null */
    private $isLoggedIn;

    public function __construct($canCurrentUser = null, $isLoggedIn = null)
    {
        $this->canCurrentUser = $canCurrentUser;
        $this->isLoggedIn = $isLoggedIn;
    }

    public function isAdmin(): bool
    {
        return $this->can('manage_options');
    }

    public function isEditor(): bool
    {
        return $this->can('edit_posts');
    }

    public function isLoggedIn(): bool
    {
        if ($this->isLoggedIn !== null) {
            return (bool) ($this->isLoggedIn)();
        }

        return function_exists('is_user_logged_in') && is_user_logged_in();
    }

    public function canAccessEditor(): bool
    {
        return $this->isEditor() || $this->isAdmin();
    }

    public function canSeeFrontendControls(): bool
    {
        return $this->canAccessEditor();
    }

    public function canSeeVisibility(Visibility $visibility): bool
    {
        return match ($visibility) {
            Visibility::Public => true,
            Visibility::Members => $this->isLoggedIn(),
            Visibility::Editorial => $this->canAccessEditor(),
        };
    }

    private function can(string $capability): bool
    {
        if ($this->canCurrentUser !== null) {
            return (bool) ($this->canCurrentUser)($capability);
        }

        return function_exists('current_user_can') && current_user_can($capability);
    }
}
