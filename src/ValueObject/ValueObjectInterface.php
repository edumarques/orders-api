<?php

declare(strict_types=1);

namespace App\ValueObject;

interface ValueObjectInterface
{
    public static function create(): static;
}
