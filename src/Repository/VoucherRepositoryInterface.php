<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Voucher;
use App\Enum\VoucherStatusEnum;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface VoucherRepositoryInterface extends RepositoryInterface
{
    /**
     * @param mixed[] $options
     *
     * @return PaginationInterface<Voucher>
     */
    public function findAllPaginated(
        VoucherStatusEnum $status,
        int $page = 1,
        int $limit = null,
        array $options = []
    ): PaginationInterface;
}
