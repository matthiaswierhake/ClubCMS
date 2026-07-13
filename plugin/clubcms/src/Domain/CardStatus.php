<?php

declare(strict_types=1);

namespace ClubCMS\Domain;

enum CardStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}
