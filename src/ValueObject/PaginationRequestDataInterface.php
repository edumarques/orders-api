<?php

declare(strict_types=1);

namespace App\ValueObject;

interface PaginationRequestDataInterface
{
    public function getPageNumber(): int;

    public function setPageNumber(int $pageNumber): static;

    public function getPageSize(): int;

    public function setPageSize(int $pageSize): static;
}
