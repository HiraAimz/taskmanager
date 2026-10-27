<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case Admin = 'Admin';
    case Manager = 'Manager';
    case Member = 'Member';
}
