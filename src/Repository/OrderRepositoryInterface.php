<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface OrderRepositoryInterface extends RepositoryInterface
{
    /**
     * @param mixed[] $options
     *
     * @return PaginationInterface<Order>
     */
    public function findAllPaginated(
        int $page = 1,
        int $limit = null,
        array $options = []
    ): PaginationInterface;
}
