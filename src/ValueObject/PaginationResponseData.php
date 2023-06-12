<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Enum\PaginationEnum;

class PaginationResponseData extends AbstractValueObject implements PaginationResponseDataInterface
{
    protected ?int $pageNumber = null;

    protected ?int $pageSize = null;

    protected ?int $totalOfItems = null;

    protected ?int $numberOfPages = null;

    /**
     * @codeCoverageIgnore
     */
    public function getPageNumber(): ?int
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
    public function getPageSize(): ?int
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

    /**
     * @codeCoverageIgnore
     */
    public function getTotalOfItems(): ?int
    {
        return $this->totalOfItems;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setTotalOfItems(int $totalOfItems): static
    {
        $this->totalOfItems = $totalOfItems;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getNumberOfPages(): ?int
    {
        return $this->numberOfPages;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setNumberOfPages(int $numberOfPages): static
    {
        $this->numberOfPages = $numberOfPages;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            PaginationEnum::PAGE_NUMBER->value => $this->pageNumber,
            PaginationEnum::PAGE_SIZE->value => $this->pageSize,
            PaginationEnum::TOTAL_OF_ITEMS->value => $this->totalOfItems,
            PaginationEnum::NUMBER_OF_PAGES->value => $this->numberOfPages,
        ];
    }
}
