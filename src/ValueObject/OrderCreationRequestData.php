<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Enum\OrderEnum;
use Symfony\Component\HttpFoundation\Request;

final class OrderCreationRequestData extends AbstractRequestData
{
    private ?float $amount = null;

    private ?string $voucherUuid = null;

    /**
     * @codeCoverageIgnore
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getVoucherUuid(): ?string
    {
        return $this->voucherUuid;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setVoucherUuid(?string $voucherUuid): self
    {
        $this->voucherUuid = $voucherUuid;

        return $this;
    }

    public static function createFromRequest(Request $request): static
    {
        $return = parent::createFromRequest($request);

        $payload = self::getPayloadFromRequest($request);

        $amount = $payload[OrderEnum::AMOUNT->value] ?? null;
        $amount = is_numeric($amount) ? (float) $amount : null;

        $voucherUuid = $payload[OrderEnum::VOUCHER_UUID->value] ?? null;

        return $return
            ->setAmount($amount)
            ->setVoucherUuid($voucherUuid);
    }
}
