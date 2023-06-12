<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Entity\EntityInterface;
use App\Entity\Order;
use App\Enum\DateTimeEnum as DateEnum;
use App\Enum\OrderEnum;

final readonly class OrderMapper implements ArrayMapperInterface
{
    /**
     * @inheritDoc
     *
     * @param Order $entity
     */
    public static function fromEntityToArray(EntityInterface $entity): array
    {
        return [
            OrderEnum::UUID->value => $entity->getUuid()->toString(),
            OrderEnum::AMOUNT->value => $entity->getAmount(),
            OrderEnum::VOUCHER_UUID->value => $entity->getVoucher()?->getUuid()->toString(),
            OrderEnum::CREATED_AT->value => $entity->getCreatedAt()->format(DateEnum::DATE_TIME_FORMAT->value),
            OrderEnum::UPDATED_AT->value => $entity->getUpdatedAt()->format(DateEnum::DATE_TIME_FORMAT->value),
        ];
    }
}
