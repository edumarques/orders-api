<?php

declare(strict_types=1);

namespace App\Enum;

enum PaginationEnum: string
{
    case PAGE_NUMBER = 'pageNumber';
    case PAGE_SIZE = 'pageSize';
    case TOTAL_OF_ITEMS = 'totalOfItems';
    case NUMBER_OF_PAGES = 'numberOfPages';
}
