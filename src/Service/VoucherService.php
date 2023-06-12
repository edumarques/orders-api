<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\VoucherCreationDto;
use App\Dto\VoucherListDto;
use App\Dto\VoucherRemovalDto;
use App\Dto\VoucherUpdateDto;
use App\Entity\Voucher;
use App\Mapper\PaginationMapper;
use App\Mapper\VoucherMapper;
use App\Repository\VoucherRepositoryInterface;
use App\ValueObject\RequestDataInterface;
use App\ValueObject\VoucherCreationRequestData;
use App\ValueObject\VoucherListRequestData;
use App\ValueObject\VoucherRemovalRequestData;
use App\ValueObject\VoucherUpdateRequestData;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class VoucherService extends AbstractApiHttpService implements VoucherServiceInterface
{
    public function __construct(
        TranslatorInterface $translator,
        private VoucherRepositoryInterface $voucherRepository,
    ) {
        parent::__construct($translator);
    }

    /**
     * @param VoucherCreationRequestData $requestData
     */
    public function create(RequestDataInterface $requestData): VoucherCreationDto
    {
        $this->validateRequestData($requestData);

        $voucher = (new Voucher())
            ->setType($requestData->getType())
            ->setDiscount($requestData->getDiscount())
            ->setExpirationDate($requestData->getExpirationDate());

        $this->voucherRepository->save($voucher);

        return VoucherCreationDto::create()
            ->setMessage($this->translator->trans('response.success.voucher.created_successfully'))
            ->setVoucher($voucher);
    }

    /**
     * @param VoucherListRequestData $requestData
     */
    public function list(RequestDataInterface $requestData): VoucherListDto
    {
        $this->validateRequestData($requestData);

        $pagination = $this->voucherRepository->findAllPaginated(
            $requestData->getStatus(),
            $requestData->getPageNumber(),
            $requestData->getPageSize()
        );

        $list = array_map(
            static fn(Voucher $voucher): array => VoucherMapper::fromEntityToArray($voucher),
            (array) $pagination->getItems()
        );

        $paginationResponseData = PaginationMapper::fromPaginationToPaginationData($pagination);

        return VoucherListDto::create()
            ->setList($list)
            ->setPaginationResponseData($paginationResponseData);
    }

    /**
     * @param VoucherUpdateRequestData $requestData
     */
    public function update(RequestDataInterface $requestData): VoucherUpdateDto
    {
        $this->validateRequestData($requestData);

        $uuid = $requestData->getUuid();
        $type = $requestData->getType();
        $discount = $requestData->getDiscount();
        $expirationDate = $requestData->getExpirationDate();

        /** @var Voucher $voucher */
        $voucher = $this->voucherRepository->findOneBy(['uuid' => $uuid]);

        if (false === empty($type)) {
            $voucher->setType($type);
        }

        if (false === empty($discount)) {
            $voucher->setDiscount($discount);
        }

        if (false === empty($expirationDate)) {
            $voucher->setExpirationDate($expirationDate);
        }

        $this->voucherRepository->save($voucher);

        return VoucherUpdateDto::create()
            ->setMessage($this->translator->trans('response.success.voucher.updated_successfully'))
            ->setVoucher($voucher);
    }

    /**
     * @param VoucherRemovalRequestData $requestData
     */
    public function delete(RequestDataInterface $requestData): VoucherRemovalDto
    {
        $this->validateRequestData($requestData);

        $uuid = $requestData->getUuid();

        /** @var Voucher $voucher */
        $voucher = $this->voucherRepository->findOneBy(['uuid' => $uuid]);

        $this->voucherRepository->delete($voucher);

        return VoucherRemovalDto::create()
            ->setMessage($this->translator->trans('response.success.voucher.removed_successfully'))
            ->setUuid($voucher->getUuid());
    }
}
