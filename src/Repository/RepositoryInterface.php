<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ObjectRepository;

interface RepositoryInterface extends ObjectRepository, Selectable
{
    /**
     * @psalm-param 0|1|2|4|null $lockMode
     * @throws EntityNotFoundException
     */
    public function findOrThrow(
        int $id,
        bool $throw = true,
        ?int $lockMode = null,
        ?int $lockVersion = null
    ): ?object;

    public function save(object $object): void;

    public function delete(object $object): void;
}
