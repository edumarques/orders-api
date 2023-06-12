<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Enum\DateTimeEnum;
use App\Enum\VoucherEnum;
use App\Enum\VoucherTypeEnum;
use Symfony\Component\HttpFoundation\Request;

final class VoucherCreationRequestData extends AbstractRequestData
{
    private ?VoucherTypeEnum $type = null;

    private ?float $discount = null;

    private \DateTimeInterface|false|null $expirationDate = null;

    /**
     * @codeCoverageIgnore
     */
    public function getType(): ?VoucherTypeEnum
    {
        return $this->type;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setType(?VoucherTypeEnum $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setDiscount(?float $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getExpirationDate(): \DateTimeInterface|false|null
    {
        return $this->expirationDate;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setExpirationDate(\DateTimeInterface|false|null $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public static function createFromRequest(Request $request): static
    {
        $return = parent::createFromRequest($request);

        $payload = self::getPayloadFromRequest($request);

        $type = VoucherTypeEnum::tryFrom($payload[VoucherEnum::TYPE->value] ?? '');

        $discount = $payload[VoucherEnum::DISCOUNT->value] ?? null;
        $discount = is_numeric($discount) ? (float) $discount : null;

        $expirationDateString = $payload[VoucherEnum::EXPIRATION_DATE->value] ?? null;

        $expirationDate = null !== $expirationDateString
            ? \DateTimeImmutable::createFromFormat(DateTimeEnum::DATE_FORMAT->value, $expirationDateString)
            : null;

        $expirationDate = $expirationDate instanceof \DateTimeInterface
            ? $expirationDate->setTime(0, 0)
            : $expirationDate;

        return $return
            ->setType($type)
            ->setDiscount($discount)
            ->setExpirationDate($expirationDate);
    }
}
