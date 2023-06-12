<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\InvalidRequestDataException;
use App\ValueObject\ValidatableDataInterface;

trait RequestValidationAwareTrait
{
    protected function validateRequestData(
        ValidatableDataInterface $requestData,
        string $defaultTranslationKey = 'exception.message.request_data_not_validated'
    ): void {
        if (!$requestData->isValid()) {
            throw new InvalidRequestDataException(
                $this->translator->trans($defaultTranslationKey)
            );
        }
    }
}
