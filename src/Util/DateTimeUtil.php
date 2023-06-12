<?php

declare(strict_types=1);

namespace App\Util;

class DateTimeUtil
{
    /**
     * @codeCoverageIgnore
     */
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
