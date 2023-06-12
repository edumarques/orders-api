<?php

declare(strict_types=1);

namespace App\Exception;

trait HttpExceptionTrait
{
    protected ?string $jsonMessage = null;

    /**
     * @codeCoverageIgnore
     */
    public function getJsonMessage(): ?string
    {
        return $this->jsonMessage;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setJsonMessage(?string $jsonMessage): static
    {
        $this->jsonMessage = $jsonMessage;

        return $this;
    }
}
