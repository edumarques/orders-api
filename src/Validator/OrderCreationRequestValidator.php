<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\Voucher;
use App\Exception\ApiHttpExceptionInterface;
use App\Exception\InvalidRequestDataException;
use App\Repository\VoucherRepositoryInterface;
use App\ValueObject\OrderCreationRequestData;
use App\ValueObject\RequestDataInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class OrderCreationRequestValidator extends AbstractRequestValidator
{
    public function __construct(
        TranslatorInterface $translator,
        private VoucherRepositoryInterface $voucherRepository
    ) {
        parent::__construct($translator);
    }

    /**
     * @param OrderCreationRequestData $requestData
     *
     * @throws ApiHttpExceptionInterface
     */
    public function validate(RequestDataInterface $requestData): void
    {
        $amount = $requestData->getAmount();

        if (null === $amount || false === $amount || 0 > $amount) {
            throw (new InvalidRequestDataException())->setJsonMessage(
                $this->translator->trans('response.error.validation.order.amount_must_be_provided_and_valid')
            );
        }

        $voucherUuid = $requestData->getVoucherUuid();

        if (null === $voucherUuid) {
            $requestData->setValid(true);
            return;
        }

        if (false === Uuid::isValid($voucherUuid)) {
            throw (new InvalidRequestDataException())->setJsonMessage(
                $this->translator->trans('response.error.validation.order.voucher_uuid_is_not_valid')
            );
        }

        /** @var Voucher|null $voucher */
        $voucher = $this->voucherRepository->findOneBy(['uuid' => $voucherUuid]);

        if (null === $voucher) {
            throw (new InvalidRequestDataException())->setJsonMessage(
                $this->translator->trans('response.error.validation.order.voucher_not_found')
            );
        }

        if ($voucher->isUsed()) {
            throw (new InvalidRequestDataException())->setJsonMessage(
                $this->translator->trans('response.error.validation.order.voucher_already_used')
            );
        }

        if ($voucher->isExpired()) {
            throw (new InvalidRequestDataException())->setJsonMessage(
                $this->translator->trans('response.error.validation.order.voucher_expired')
            );
        }

        $requestData->setValid(true);
    }
}
