<?php

declare(strict_types=1);

namespace App\Validator;

use App\ValueObject\OrderListRequestData;
use App\ValueObject\RequestDataInterface;

final readonly class OrderListRequestValidator extends AbstractRequestValidator
{
    /**
     * @param OrderListRequestData $requestData
     */
    public function validate(RequestDataInterface $requestData): void
    {
        $requestData->setValid(true);
    }
}
