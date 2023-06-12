<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Order findOrThrow(int $id, bool $throw = true, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Order[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method Order[] findAll()
 * @method void save(Order $object)
 * @method void delete(Order $object)
 */
class OrderRepository extends AbstractRepository implements OrderRepositoryInterface
{
    protected string $entityClass = Order::class;

    public function __construct(
        ManagerRegistry $managerRegistry,
        protected readonly PaginatorInterface $paginator
    ) {
        parent::__construct($managerRegistry);
    }

    /**
     * @inheritDoc
     */
    public function findAllPaginated(
        int $page = 1,
        int $limit = null,
        array $options = []
    ): PaginationInterface {
        return $this->paginator->paginate(
            $this->getQueryBuilderForAll(),
            $page,
            $limit,
            $options
        );
    }

    protected function getQueryBuilderForAll(): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.id', Criteria::DESC);
    }
}
