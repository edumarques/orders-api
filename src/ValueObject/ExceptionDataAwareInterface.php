<?php

declare(strict_types=1);

namespace App\ValueObject;

interface ExceptionDataAwareInterface
{
    public function getException(): ?string;

    public function setException(?string $exceptionMessage): static;

    /**
     * @return array<array<string, string|int>>|null
     */
    public function getExceptionTrace(): ?array;

    /**
     * @param array<int, array<string, int|string|null>>|null $exceptionTrace
     */
    public function setExceptionTrace(?array $exceptionTrace): static;
}
