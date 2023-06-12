<?php

declare(strict_types=1);

namespace App\ValueObject;

final class VoucherRemovalRequestData extends AbstractRequestData
{
    private ?string $uuid = null;

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
}
