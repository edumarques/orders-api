<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractRepository extends ServiceEntityRepository implements RepositoryInterface
{
    protected string $entityClass;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, $this->entityClass);
    }

    /**
     * @psalm-param 0|1|2|4|null $lockMode
     * @throws EntityNotFoundException
     */
    public function findOrThrow(
        int $id,
        bool $throw = true,
        ?int $lockMode = null,
        ?int $lockVersion = null
    ): ?object {
        $return = $this->find($id, $lockMode, $lockVersion);

        if (null === $return && $throw) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(static::class, [(string) $id]);
        }

        return $return;
    }

    public function save(object $object): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($object);
        $entityManager->flush();
    }

    public function delete(object $object): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($object);
        $entityManager->flush();
    }
}
