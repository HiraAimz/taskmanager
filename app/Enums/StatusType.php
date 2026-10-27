<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusType: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';
}
