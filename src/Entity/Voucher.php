<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\VoucherStatusEnum;
use App\Enum\VoucherTypeEnum;
use App\Repository\VoucherRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: VoucherRepository::class)]
#[ORM\Table(name: 'vouchers')]
#[ORM\Index(fields: ['expirationDate'], name: 'expiration_date_idx')]
#[ORM\Index(fields: ['order', 'expirationDate'], name: 'is_active_idx')]
class Voucher implements EntityInterface, UuidAwareEntityInterface, TimestampableEntityInterface
{
    use EntityTrait;
    use UuidAwareEntityTrait;
    use TimestampableEntityTrait;

    #[ORM\OneToOne(inversedBy: 'voucher', targetEntity: Order::class, cascade: ['all'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    protected ?Order $order;

    #[ORM\Column(type: 'voucher_type')]
    protected VoucherTypeEnum $type;

    #[ORM\Column(type: Types::FLOAT)]
    protected float $discount;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $expirationDate;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4();
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getType(): VoucherTypeEnum
    {
        return $this->type;
    }

    public function setType(VoucherTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): static
    {
        $this->discount = $discount;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeInterface $expirationDate): static
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function hasPercentageDiscount(): bool
    {
        return $this->type === VoucherTypeEnum::PERCENTAGE;
    }

    public function isUsed(): bool
    {
        return $this->order !== null;
    }

    public function expires(): bool
    {
        return $this->expirationDate !== null;
    }

    public function isExpired(): bool
    {
        if (false === $this->expires()) {
            return false;
        }

        $nowTimestamp = (new \DateTimeImmutable())->getTimestamp();

        return $nowTimestamp > $this->expirationDate->getTimestamp();
    }

    public function isActive(): bool
    {
        return false === $this->isUsed() && false === $this->isExpired();
    }

    public function getStatus(): ?VoucherStatusEnum
    {
        return match (true) {
            $this->isExpired() => VoucherStatusEnum::EXPIRED,
            $this->isActive() => VoucherStatusEnum::ACTIVE,
            $this->isUsed() => VoucherStatusEnum::USED,
            default => null
        };
    }
}
