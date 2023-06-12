<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Voucher;

final class VoucherCreationDto extends AbstractDto
{
    private string $message;

    private Voucher $voucher;

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getVoucher(): Voucher
    {
        return $this->voucher;
    }

    public function setVoucher(Voucher $voucher): self
    {
        $this->voucher = $voucher;

        return $this;
    }
}
