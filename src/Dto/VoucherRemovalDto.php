<?php

declare(strict_types=1);

namespace App\Dto;

use Ramsey\Uuid\UuidInterface;

final class VoucherRemovalDto extends AbstractDto
{
    private string $message;

    private UuidInterface $uuid;

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function setUuid(UuidInterface $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}
