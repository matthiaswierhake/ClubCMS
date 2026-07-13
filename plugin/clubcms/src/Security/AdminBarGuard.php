<?php

declare(strict_types=1);

namespace ClubCMS\Security;

final class AdminBarGuard
{
    /** @var callable(string): bool|null */
    private $canManageOptions;

    public function __construct($canManageOptions = null)
    {
        $this->canManageOptions = $canManageOptions;
    }

    public function filter(bool $show): bool
    {
        return $this->canManageOptions() ? $show : false;
    }

    private function canManageOptions(): bool
    {
        if ($this->canManageOptions !== null) {
            return (bool) ($this->canManageOptions)('manage_options');
        }

        return function_exists('current_user_can') && current_user_can('manage_options');
    }
}
