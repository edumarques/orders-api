<?php

declare(strict_types=1);

namespace App\Dto;

final class OrderListDto extends AbstractDto implements ListableDtoInterface, PaginationResponseDataAwareDtoInterface
{
    use ListableDtoTrait;
    use PaginationResponseDataAwareDtoTrait;
}
