<?php

declare(strict_types=1);

namespace App\Entity;

use Ramsey\Uuid\UuidInterface;

interface UuidAwareEntityInterface
{
    public function getUuid(): UuidInterface;
}
