<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\Voucher;
use App\Exception\ApiHttpExceptionInterface;
use App\Exception\InvalidRequestDataException;
use App\Repository\VoucherRepositoryInterface;
use App\ValueObject\RequestDataInterface;
use App\ValueObject\VoucherUpdateRequestData;
use Ramsey\Uuid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class VoucherUpdateRequestValidator extends AbstractRequestValidator
{
    public function __construct(
        TranslatorInterface $translator,
        private VoucherRepositoryInterface $voucherRepository
    ) {
        parent::__construct($translator);
    }

    /**
     * @param VoucherUpdateRequestData $requestData
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

        /** @var Voucher|null $voucher */
        $voucher = $this->voucherRepository->findOneBy(['uuid' => $uuid]);

        if (null === $voucher) {
            throw (new InvalidRequestDataException())->setJsonMessage(
                $this->translator->trans('response.error.validation.voucher.not_found_for_provided_uuid')
            );
        }

        if (false === $voucher->isActive()) {
            throw (new InvalidRequestDataException())->setJsonMessage(
                $this->translator->trans('response.error.validation.voucher.must_be_active_to_be_updated')
            );
        }

        $requestData->setValid(true);
    }
}
