<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Enum\DateTimeEnum;
use App\Enum\VoucherEnum;
use App\Enum\VoucherTypeEnum;
use Symfony\Component\HttpFoundation\Request;

final class VoucherUpdateRequestData extends AbstractRequestData
{
    private ?string $uuid = null;

    private VoucherTypeEnum|false|null $type = null;

    private float|false|null $discount = null;

    private \DateTimeInterface|false|null $expirationDate = null;

    /**
     * @codeCoverageIgnore
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getType(): VoucherTypeEnum|false|null
    {
        return $this->type;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setType(VoucherTypeEnum|false|null $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDiscount(): float|false|null
    {
        return $this->discount;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setDiscount(float|false|null $discount): self
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

        try {
            $typeString = $payload[VoucherEnum::TYPE->value] ?? null;
            $type = null !== $typeString ? VoucherTypeEnum::from($payload[VoucherEnum::TYPE->value] ?? '') : null;
        } catch (\ValueError) {
            $type = false;
        }

        $discount = $payload[VoucherEnum::DISCOUNT->value] ?? null;
        $discount = null !== $discount && false === is_float($discount) ? false : $discount;

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
