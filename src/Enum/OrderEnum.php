<?php

declare(strict_types=1);

namespace App\Enum;

enum OrderEnum: string
{
    case UUID = 'uuid';
    case AMOUNT = 'amount';
    case VOUCHER_UUID = 'voucherUuid';
    case CREATED_AT = 'createdAt';
    case UPDATED_AT = 'updatedAt';
}
