<?php

declare(strict_types=1);

namespace App\Dto;

trait ListableDtoTrait
{
    /**
     * @var mixed[]
     */
    private array $list;

    /**
     * @return  mixed[]
     */
    public function getList(): array
    {
        return $this->list;
    }

    /**
     * @param mixed[] $list
     */
    public function setList(array $list): static
    {
        $this->list = $list;

        return $this;
    }
}
