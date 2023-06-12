<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\OrderCreationDto;
use App\Dto\OrderListDto;
use App\Entity\Order;
use App\Entity\Voucher;
use App\Mapper\OrderMapper;
use App\Mapper\PaginationMapper;
use App\Repository\OrderRepositoryInterface;
use App\Repository\VoucherRepositoryInterface;
use App\ValueObject\OrderCreationRequestData;
use App\ValueObject\OrderListRequestData;
use App\ValueObject\RequestDataInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class OrderService extends AbstractApiHttpService implements OrderServiceInterface
{
    public function __construct(
        TranslatorInterface $translator,
        private OrderRepositoryInterface $orderRepository,
        private VoucherRepositoryInterface $voucherRepository,
    ) {
        parent::__construct($translator);
    }

    /**
     * @param OrderCreationRequestData $requestData
     */
    public function create(RequestDataInterface $requestData): OrderCreationDto
    {
        $this->validateRequestData($requestData);

        $orderAmount = $requestData->getAmount();
        $voucherUuid = $requestData->getVoucherUuid();

        /** @var Voucher|null $voucher */
        $voucher = null !== $voucherUuid
            ? $this->voucherRepository->findOneBy(['uuid' => $voucherUuid])
            : null;

        if (null !== $voucher) {
            $voucherDiscount = $voucher->getDiscount();

            $orderDiscount = $voucher->hasPercentageDiscount()
                ? ($voucherDiscount / 100) * $orderAmount
                : $voucherDiscount;

            $orderAmount -= $orderDiscount;
            $orderAmount = 0 > $orderAmount ? 0 : $orderAmount;
        }

        $order = (new Order())
            ->setAmount($orderAmount)
            ->setVoucher($voucher);

        $this->orderRepository->save($order);

        return OrderCreationDto::create()
            ->setMessage($this->translator->trans('response.success.order.created_successfully'))
            ->setOrder($order);
    }

    /**
     * @param OrderListRequestData $requestData
     */
    public function list(RequestDataInterface $requestData): OrderListDto
    {
        $this->validateRequestData($requestData);

        $pagination = $this->orderRepository->findAllPaginated(
            $requestData->getPageNumber(),
            $requestData->getPageSize()
        );

        $list = array_map(
            static fn(Order $order): array => OrderMapper::fromEntityToArray($order),
            (array) $pagination->getItems()
        );

        $paginationResponseData = PaginationMapper::fromPaginationToPaginationData($pagination);

        return OrderListDto::create()
            ->setList($list)
            ->setPaginationResponseData($paginationResponseData);
    }
}
