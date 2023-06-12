<?php

declare(strict_types=1);

namespace App\Validator;

use App\ValueObject\RequestDataInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

interface RequestValidatorInterface
{
    /**
     * @throws HttpExceptionInterface
     */
    public function validate(RequestDataInterface $requestData): void;
}
