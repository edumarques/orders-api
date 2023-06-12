<?php

declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\HttpFoundation\Request;

interface RequestDataInterface
{
    public static function createFromRequest(Request $request): static;

    public function getQueryString(): ?string;

    public function setQueryString(?string $queryString): static;

    public function getRawPayload(): ?string;

    public function setRawPayload(?string $rawPayload): static;
}
