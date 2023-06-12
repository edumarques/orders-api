<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Entity\EntityInterface;
use App\Entity\Voucher;
use App\Enum\DateTimeEnum as DateEnum;
use App\Enum\VoucherEnum;

final readonly class VoucherMapper implements ArrayMapperInterface
{
    /**
     * @inheritDoc
     *
     * @param Voucher $entity
     */
    public static function fromEntityToArray(EntityInterface $entity): array
    {
        return [
            VoucherEnum::UUID->value => $entity->getUuid()->toString(),
            VoucherEnum::TYPE->value => $entity->getType()->value,
            VoucherEnum::DISCOUNT->value => $entity->getDiscount(),
            VoucherEnum::STATUS->value => $entity->getStatus()?->value,
            VoucherEnum::ORDER_UUID->value => $entity->getOrder()?->getUuid()->toString(),
            VoucherEnum::EXPIRATION_DATE->value => $entity->getExpirationDate()->format(DateEnum::DATE_FORMAT->value),
            VoucherEnum::CREATED_AT->value => $entity->getCreatedAt()->format(DateEnum::DATE_TIME_FORMAT->value),
            VoucherEnum::UPDATED_AT->value => $entity->getUpdatedAt()->format(DateEnum::DATE_TIME_FORMAT->value),
        ];
    }
}
