<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\DtoInterface;
use App\ValueObject\RequestDataInterface;

interface VoucherServiceInterface
{
    public function create(RequestDataInterface $requestData): DtoInterface;

    public function list(RequestDataInterface $requestData): DtoInterface;

    public function update(RequestDataInterface $requestData): DtoInterface;

    public function delete(RequestDataInterface $requestData): DtoInterface;
}
