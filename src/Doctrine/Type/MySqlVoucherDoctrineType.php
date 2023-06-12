<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Enum\VoucherTypeEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * @codeCoverageIgnore
 */
final class MySqlVoucherDoctrineType extends AbstractVoucherDoctrineType
{
    /**
     * @inheritDoc
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return sprintf(
            "ENUM('%s', '%s')",
            VoucherTypeEnum::CONCRETE->value,
            VoucherTypeEnum::PERCENTAGE->value
        );
    }
}
