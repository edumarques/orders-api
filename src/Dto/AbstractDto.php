<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractDto implements DtoInterface
{
    final protected function __construct()
    {
    }

    public static function create(): static
    {
        return new static();
    }
}
