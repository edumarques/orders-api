<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Enum\ResponseEnum;
use App\Enum\ResponseStatusEnum;

class FailedResponseData extends AbstractResponseData implements ExceptionDataAwareInterface
{
    protected string $status = ResponseStatusEnum::STATUS_ERROR->value;

    protected ?string $exception = null;

    /**
     * @var array<array<string, string|int>>|null
     */
    protected ?array $exceptionTrace = null;

    /**
     * @codeCoverageIgnore
     */
    public function getException(): ?string
    {
        return $this->exception;
    }

    /**
     * @@codeCoverageIgnore
     */
    public function setException(?string $exception): static
    {
        $this->exception = $exception;

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @codeCoverageIgnore
     */
    public function getExceptionTrace(): ?array
    {
        return $this->exceptionTrace;
    }

    /**
     * @inheritDoc
     *
     * @codeCoverageIgnore
     */
    public function setExceptionTrace(?array $exceptionTrace): static
    {
        $this->exceptionTrace = $exceptionTrace;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(bool $includeExceptionData = true): array
    {
        $return = parent::toArray();

        if (false === $includeExceptionData) {
            return $return;
        }

        return [
            ...$return,
            ResponseEnum::EXCEPTION->value => $this->exception,
            ResponseEnum::EXCEPTION_TRACE->value => $this->exceptionTrace,
        ];
    }
}
