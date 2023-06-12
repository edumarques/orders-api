<?php

declare(strict_types=1);

namespace App\Dto;

interface ListableDtoInterface
{
    /**
     * @return mixed[]
     */
    public function getList(): array;

    /**
     * @param mixed[] $list
     */
    public function setList(array $list): static;
}
