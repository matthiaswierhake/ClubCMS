<?php

declare(strict_types=1);

namespace ClubCMS\Security;

final class AdminBarGuard
{
    private AccessRoleModel $roles;

    public function __construct($canManageOptions = null)
    {
        $this->roles = new AccessRoleModel($canManageOptions);
    }

    public function filter(bool $show): bool
    {
        return $this->roles->isAdmin() ? $show : false;
    }
}
