<?php

declare(strict_types=1);

namespace App\Entity;

use Gedmo\Timestampable\Timestampable;

interface TimestampableEntityInterface extends Timestampable
{
    public function getCreatedAt(): ?\DateTimeInterface;

    public function getUpdatedAt(): ?\DateTimeInterface;
}
