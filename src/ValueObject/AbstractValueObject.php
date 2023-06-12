<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractValueObject implements ValueObjectInterface
{
    final protected function __construct()
    {
    }

    public static function create(): static
    {
        return new static();
    }
}
