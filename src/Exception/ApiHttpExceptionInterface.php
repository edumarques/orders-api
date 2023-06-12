<?php

declare(strict_types=1);

namespace App\Exception;

interface ApiHttpExceptionInterface extends \Throwable
{
    public function getJsonMessage(): ?string;

    public function setJsonMessage(?string $jsonMessage): static;
}
