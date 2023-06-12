<?php

declare(strict_types=1);

namespace App\Util;

final readonly class DateTimeUtil
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
