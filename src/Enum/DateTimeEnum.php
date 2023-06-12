<?php

declare(strict_types=1);

namespace App\Enum;

enum DateTimeEnum: string
{
    case DATE_FORMAT = 'Y-m-d';
    case DATE_TIME_FORMAT = 'Y-m-d H:i:s';
}
