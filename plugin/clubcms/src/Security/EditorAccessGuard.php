<?php

declare(strict_types=1);

namespace ClubCMS\Security;

final class EditorAccessGuard
{
    private AccessRoleModel $roles;

    public function __construct($canCurrentUser = null)
    {
        $this->roles = new AccessRoleModel($canCurrentUser);
    }

    public function canAccess(): bool
    {
        return $this->roles->canAccessEditor();
    }
}
