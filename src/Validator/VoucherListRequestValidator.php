<?php

declare(strict_types=1);

namespace App\Validator;

use App\ValueObject\RequestDataInterface;
use App\ValueObject\VoucherListRequestData;

final readonly class VoucherListRequestValidator extends AbstractRequestValidator
{
    /**
     * @param VoucherListRequestData $requestData
     */
    public function validate(RequestDataInterface $requestData): void
    {
        $requestData->setValid(true);
    }
}
