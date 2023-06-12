<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Entity\EntityInterface;

interface ArrayMapperInterface
{
    /**
     * @return mixed[]
     */
    public static function fromEntityToArray(EntityInterface $entity): array;
}
