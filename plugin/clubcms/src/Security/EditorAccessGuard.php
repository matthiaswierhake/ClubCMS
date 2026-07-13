<?php

declare(strict_types=1);

namespace ClubCMS\Security;

final class EditorAccessGuard
{
    /** @var callable(string): bool|null */
    private $canCurrentUser;

    public function __construct($canCurrentUser = null)
    {
        $this->canCurrentUser = $canCurrentUser;
    }

    public function canAccess(): bool
    {
        return $this->canEditPosts() || $this->canManageOptions();
    }

    private function canEditPosts(): bool
    {
        if ($this->canCurrentUser !== null) {
            return (bool) ($this->canCurrentUser)('edit_posts');
        }

        return function_exists('current_user_can') && current_user_can('edit_posts');
    }

    private function canManageOptions(): bool
    {
        if ($this->canCurrentUser !== null) {
            return (bool) ($this->canCurrentUser)('manage_options');
        }

        return function_exists('current_user_can') && current_user_can('manage_options');
    }
}
