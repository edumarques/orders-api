<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
#[ORM\Index(fields: ['createdAt'], name: 'created_at_idx')]
class Order implements EntityInterface, UuidAwareEntityInterface, TimestampableEntityInterface
{
    use EntityTrait;
    use UuidAwareEntityTrait;
    use TimestampableEntityTrait;

    #[ORM\Column(type: Types::FLOAT)]
    protected float $amount;

    #[ORM\OneToOne(mappedBy: 'order', targetEntity: Voucher::class, cascade: ['all'])]
    protected ?Voucher $voucher;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4();
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getVoucher(): ?Voucher
    {
        return $this->voucher;
    }

    public function setVoucher(?Voucher $voucher): static
    {
        $this->voucher = $voucher;

        $voucher->setOrder($this);

        return $this;
    }
}
