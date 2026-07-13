<?php

declare(strict_types=1);

namespace ClubCMS\Domain;

enum Visibility: string
{
    case Public = 'public';
    case Members = 'members';
    case Editorial = 'editorial';
}
