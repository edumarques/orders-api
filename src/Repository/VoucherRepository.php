<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Voucher;
use App\Enum\VoucherStatusEnum;
use App\Util\DateTimeUtil;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Voucher findOrThrow(int $id, bool $throw = true, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Voucher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voucher|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Voucher[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method Voucher[] findAll()
 * @method void save(Voucher $object)
 * @method void delete(Voucher $object)
 */
class VoucherRepository extends AbstractRepository implements VoucherRepositoryInterface
{
    protected string $entityClass = Voucher::class;

    public function __construct(
        ManagerRegistry $managerRegistry,
        protected readonly PaginatorInterface $paginator,
        protected readonly DateTimeUtil $dateTimeUtil
    ) {
        parent::__construct($managerRegistry);
    }

    /**
     * @inheritDoc
     */
    public function findAllPaginated(
        ?VoucherStatusEnum $status = null,
        int $page = 1,
        int $limit = null,
        array $options = []
    ): PaginationInterface {
        return $this->paginator->paginate(
            $this->getQueryBuilderForAll($status),
            $page,
            $limit,
            $options
        );
    }

    protected function getQueryBuilderForAll(?VoucherStatusEnum $status): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('voucher')
            ->orderBy('voucher.id', Criteria::DESC);

        switch ($status) {
            case VoucherStatusEnum::EXPIRED:
                $queryBuilder->where('voucher.expirationDate < :dateNow')
                    ->setParameter('dateNow', $this->dateTimeUtil->now());

                break;
            case VoucherStatusEnum::ACTIVE:
                $queryBuilder->where('voucher.expirationDate >= :dateNow')
                    ->andWhere('voucher.order IS NULL')
                    ->setParameter('dateNow', $this->dateTimeUtil->now());

                break;
            case VoucherStatusEnum::USED:
                $queryBuilder->where('voucher.order IS NOT NULL');
        }

        return $queryBuilder;
    }
}
