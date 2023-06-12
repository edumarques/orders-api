<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Enum\PaginationEnum;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractRequestData extends AbstractValueObject implements
    RequestDataInterface,
    ValidatableDataInterface,
    PaginationRequestDataInterface
{
    protected bool $valid = false;

    protected ?string $queryString = null;

    protected ?string $rawPayload = null;

    protected int $pageNumber = 1;

    protected int $pageSize = 10;

    /**
     * @codeCoverageIgnore
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setValid(bool $valid): static
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getQueryString(): ?string
    {
        return $this->queryString;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setQueryString(?string $queryString): static
    {
        $this->queryString = $queryString;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getRawPayload(): ?string
    {
        return $this->rawPayload;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setRawPayload(?string $rawPayload): static
    {
        $this->rawPayload = $rawPayload;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setPageNumber(int $pageNumber): static
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setPageSize(int $pageSize): static
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    public static function createFromRequest(Request $request): static
    {
        $return = static::create();

        $pageNumber = $request->query->getInt(PaginationEnum::PAGE_NUMBER->value, $return->getPageNumber());
        $pageNumber = $pageNumber > 0 ? $pageNumber : $return->getPageNumber();

        $pageSize = $request->query->getInt(PaginationEnum::PAGE_SIZE->value, $return->getPageSize());
        $pageSize = $pageSize > 0 ? $pageSize : $return->getPageSize();

        $return->setQueryString($request->getQueryString())
            ->setRawPayload($request->getContent())
            ->setPageNumber($pageNumber)
            ->setPageSize($pageSize);

        return $return;
    }

    /**
     * @return array<string, mixed>|null
     * @throws JsonException
     */
    protected static function getPayloadFromRequest(Request $request, bool $throw = false): ?array
    {
        try {
            return $request->toArray();
        } catch (JsonException $exception) {
            if ($throw) {
                throw $exception;
            }

            return null;
        }
    }
}
