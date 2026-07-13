<?php

declare(strict_types=1);

namespace ClubCMS\Security;

final class AdminBarGuard
{
    public function filter(bool $show): bool
    {
        return false;
    }
}
