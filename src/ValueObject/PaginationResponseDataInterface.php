<?php

declare(strict_types=1);

namespace App\ValueObject;

interface PaginationResponseDataInterface
{
    public function getPageNumber(): ?int;

    public function setPageNumber(int $pageNumber): static;

    public function getPageSize(): ?int;

    public function setPageSize(int $pageSize): static;

    public function getTotalOfItems(): ?int;

    public function setTotalOfItems(int $totalOfItems): static;

    public function getNumberOfPages(): ?int;

    public function setNumberOfPages(int $numberOfPages): static;

    /**
     * @return array<string, int|null>
     */
    public function toArray(): array;
}
