<?php

declare(strict_types=1);

namespace App\Enum;

enum VoucherTypeEnum: string
{
    case CONCRETE = 'CONCRETE';
    case PERCENTAGE = 'PERCENTAGE';
}
