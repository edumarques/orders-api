<?php

declare(strict_types=1);

namespace App\Validator;

use App\Exception\ApiHttpExceptionInterface;
use App\Exception\InvalidRequestDataException;
use App\ValueObject\RequestDataInterface;
use App\ValueObject\VoucherCreationRequestData;

final readonly class VoucherCreationRequestValidator extends AbstractRequestValidator
{
    /**
     * @param VoucherCreationRequestData $requestData
     *
     * @throws ApiHttpExceptionInterface
     */
    public function validate(RequestDataInterface $requestData): void
    {
        if (null === $requestData->getType()) {
            throw (new InvalidRequestDataException())->setJsonMessage(
                $this->translator->trans('response.error.validation.voucher.type_must_be_provided_and_valid')
            );
        }

        if (false === $requestData->getExpirationDate()) {
            throw (new InvalidRequestDataException())->setJsonMessage(
                $this->translator->trans('response.error.validation.voucher.expiration_date_must_have_a_valid_format')
            );
        }

        $discount = $requestData->getDiscount();

        if (null === $discount || 0 > $discount) {
            throw (new InvalidRequestDataException())->setJsonMessage(
                $this->translator->trans('response.error.validation.voucher.discount_must_be_provided_and_valid')
            );
        }

        $requestData->setValid(true);
    }
}
