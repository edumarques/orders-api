<?php

declare(strict_types=1);

namespace App\ValueObject;

interface PaginationResponseDataAwareInterface
{
    public function getPaginationData(): ?PaginationResponseDataInterface;

    public function setPaginationData(?PaginationResponseDataInterface $paginationData): static;
}
