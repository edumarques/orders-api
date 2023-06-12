<?php

declare(strict_types=1);

namespace App\Dto;

use App\ValueObject\PaginationResponseDataInterface;

trait PaginationResponseDataAwareDtoTrait
{
    private PaginationResponseDataInterface $paginationResponseData;

    public function getPaginationResponseData(): PaginationResponseDataInterface
    {
        return $this->paginationResponseData;
    }

    public function setPaginationResponseData(PaginationResponseDataInterface $paginationResponseData): static
    {
        $this->paginationResponseData = $paginationResponseData;

        return $this;
    }
}
