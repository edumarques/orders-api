<?php

declare(strict_types=1);

namespace App\Validator;

use App\Exception\ApiHttpExceptionInterface;
use App\Exception\InvalidRequestDataException;
use App\Repository\VoucherRepositoryInterface;
use App\ValueObject\RequestDataInterface;
use App\ValueObject\VoucherRemovalRequestData;
use Ramsey\Uuid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class VoucherRemovalRequestValidator extends AbstractRequestValidator
{
    public function __construct(
        TranslatorInterface $translator,
        private VoucherRepositoryInterface $voucherRepository
    ) {
        parent::__construct($translator);
    }

    /**
     * @param VoucherRemovalRequestData $requestData
     *
     * @throws ApiHttpExceptionInterface
     */
    public function validate(RequestDataInterface $requestData): void
    {
        $uuid = $requestData->getUuid();

        if (null === $uuid || false === Uuid::isValid($uuid)) {
            throw (new InvalidRequestDataException())->setJsonMessage(
                $this->translator->trans('response.error.validation.voucher.uuid_must_be_provided_and_valid')
            );
        }

        $voucher = $this->voucherRepository->findOneBy(['uuid' => $uuid]);

        if (null === $voucher) {
            throw (new InvalidRequestDataException())->setJsonMessage(
                $this->translator->trans('response.error.validation.voucher.not_found_for_provided_uuid')
            );
        }

        $requestData->setValid(true);
    }
}
