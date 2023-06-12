<?php

declare(strict_types=1);

namespace App\ValueObject;

interface ResponseDataInterface
{
    public function getStatus(): ?string;

    public function getMessage(): ?string;

    public function setMessage(?string $message): static;

    /**
     * @return mixed[]|null
     */
    public function getData(): ?array;

    /**
     * @param mixed[]|null $data
     */
    public function setData(?array $data): static;

    public function getStatusCode(): ?int;

    public function setStatusCode(?int $statusCode): static;

    public static function create(): static;

    /**
     * @return array<string, string|mixed[]>
     */
    public function toArray(bool $includeExceptionData = true): array;
}
