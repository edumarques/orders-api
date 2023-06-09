<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

trait UuidAwareEntityTrait
{
    #[ORM\Column(type: 'uuid', unique: true)]
    protected UuidInterface $uuid;

    /**
     * @codeCoverageIgnore
     */
    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }
}
