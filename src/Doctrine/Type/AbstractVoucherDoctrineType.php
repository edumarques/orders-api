<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Enum\VoucherTypeEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractVoucherDoctrineType extends Type
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'voucher_type';
    }

    /**
     * @inheritDoc
     *
     * @param string $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?VoucherTypeEnum
    {
        return VoucherTypeEnum::tryFrom($value);
    }

    /**
     * @inheritDoc
     *
     * @param VoucherTypeEnum|string $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (is_string($value)) {
            return $value;
        }

        return $value->value;
    }
}
