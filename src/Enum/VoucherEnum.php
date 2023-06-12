<?php

declare(strict_types=1);

namespace App\Enum;

enum VoucherEnum: string
{
    case UUID = 'uuid';
    case TYPE = 'type';
    case DISCOUNT = 'discount';
    case EXPIRATION_DATE = 'expirationDate';
    case ORDER_UUID = 'orderUuid';
    case CREATED_AT = 'createdAt';
    case UPDATED_AT = 'updatedAt';
    case IS_USED = 'isUsed';
    case IS_EXPIRED = 'isExpired';
    case IS_ACTIVE = 'isActive';
    case STATUS = 'status';
}
