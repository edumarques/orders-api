<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Order;

final class OrderCreationDto extends AbstractDto
{
    private string $message;

    private Order $order;

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }
}
