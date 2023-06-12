<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\DtoInterface;
use App\ValueObject\RequestDataInterface;

interface OrderServiceInterface
{
    public function create(RequestDataInterface $requestData): DtoInterface;

    public function list(RequestDataInterface $requestData): DtoInterface;
}
