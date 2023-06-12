<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Enum\ResponseEnum;

abstract class AbstractResponseData extends AbstractValueObject implements
    ResponseDataInterface,
    PaginationResponseDataAwareInterface
{
    protected string $status;

    protected ?string $message = null;

    /**
     * @var mixed[]|null
     */
    protected ?array $data = null;

    protected ?PaginationResponseDataInterface $paginationData = null;

    protected ?int $statusCode = null;

    /**
     * @codeCoverageIgnore
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @codeCoverageIgnore
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     *
     * @codeCoverageIgnore
     */
    public function setData(?array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getPaginationData(): ?PaginationResponseDataInterface
    {
        return $this->paginationData;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setPaginationData(?PaginationResponseDataInterface $paginationData): static
    {
        $this->paginationData = $paginationData;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setStatusCode(?int $statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(bool $includeExceptionData = true): array
    {
        return [
            ResponseEnum::STATUS->value => $this->status,
            ResponseEnum::MESSAGE->value => $this->message,
            ResponseEnum::DATA->value => $this->data,
            ResponseEnum::PAGINATION->value => $this->paginationData?->toArray(),
        ];
    }
}
