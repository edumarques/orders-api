<?php

declare(strict_types=1);

namespace App\Enum;

enum ResponseStatusEnum: string
{
    case STATUS_SUCCESS = 'success';
    case STATUS_ERROR = 'error';
}
