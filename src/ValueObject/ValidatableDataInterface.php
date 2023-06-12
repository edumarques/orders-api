<?php

declare(strict_types=1);

namespace App\ValueObject;

interface ValidatableDataInterface
{
    public function isValid(): bool;

    public function setValid(bool $valid): static;
}
