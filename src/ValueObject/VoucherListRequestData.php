<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Enum\VoucherEnum;
use App\Enum\VoucherStatusEnum;
use Symfony\Component\HttpFoundation\Request;

final class VoucherListRequestData extends AbstractRequestData
{
    protected ?VoucherStatusEnum $status = null;

    /**
     * @codeCoverageIgnore
     */
    public function getStatus(): ?VoucherStatusEnum
    {
        return $this->status;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setStatus(?VoucherStatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public static function createFromRequest(Request $request): static
    {
        $return = parent::createFromRequest($request);

        $statusId = $request->query->get(VoucherEnum::STATUS->value);

        $status = is_numeric($statusId) ? VoucherStatusEnum::tryFrom((int) $statusId) : null;

        return $return->setStatus($status);
    }
}
