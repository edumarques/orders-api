<?php

declare(strict_types=1);

namespace App\Enum;

enum VoucherStatusEnum: int
{
    case EXPIRED = 0;
    case ACTIVE = 1;
    case USED = 2;
}
