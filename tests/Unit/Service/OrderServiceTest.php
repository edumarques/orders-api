<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Dto\OrderListDto;
use App\Entity\Order;
use App\Entity\Voucher;
use App\Enum\VoucherTypeEnum;
use App\Exception\InvalidRequestDataException;
use App\Repository\OrderRepositoryInterface;
use App\Repository\VoucherRepositoryInterface;
use App\Service\OrderService;
use App\Tests\TestHelperTrait;
use App\ValueObject\OrderCreationRequestData;
use App\ValueObject\OrderListRequestData;
use App\ValueObject\PaginationResponseData;
use Knp\Component\Pager\Pagination\PaginationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderServiceTest extends TestCase
{
    use TestHelperTrait;

    private MockObject $translator;

    private MockObject $orderRepository;

    private MockObject $voucherRepository;

    private OrderService $orderService;

    public function testCreateWithInvalidRequestData(): void
    {
        $requestData = OrderCreationRequestData::create();

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('exception.message.request_data_not_validated')
            ->willReturnArgument(0);

        $this->orderRepository->expects($this->never())->method($this->anything());
        $this->voucherRepository->expects($this->never())->method($this->anything());

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('exception.message.request_data_not_validated');

        $this->orderService->create($requestData);
    }

    public function testCreateWithPercentageVoucher(): void
    {
        $voucherUuid = Uuid::uuid4();
        $voucherUuidString = $voucherUuid->toString();
        $amount = 99.90;

        $requestData = OrderCreationRequestData::create()
            ->setAmount($amount)
            ->setVoucherUuid($voucherUuidString)
            ->setValid(true);

        $voucher = (new Voucher())
            ->setType(VoucherTypeEnum::PERCENTAGE)
            ->setDiscount(5.0);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => $voucherUuidString])
            ->willReturn($voucher);

        $this->orderRepository->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(
                    function (Order $order) use ($voucher): true {
                        $this->assertSame(94.905, $order->getAmount());
                        $this->assertEquals($voucher, $order->getVoucher());
                        $this->assertTrue(Uuid::isValid($order->getUuid()->toString()));
                        return true;
                    }
                )
            );

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.success.order.created_successfully')
            ->willReturnArgument(0);

        $actual = $this->orderService->create($requestData);
        $actualOrder = $actual->getOrder();

        $this->assertSame('response.success.order.created_successfully', $actual->getMessage());
        $this->assertSame(94.905, $actualOrder->getAmount());
        $this->assertEquals($voucher, $actualOrder->getVoucher());
        $this->assertTrue(Uuid::isValid($actualOrder->getUuid()->toString()));
    }

    public function testCreateWithConcreteVoucher(): void
    {
        $voucherUuid = Uuid::uuid4();
        $voucherUuidString = $voucherUuid->toString();
        $amount = 99.90;

        $requestData = OrderCreationRequestData::create()
            ->setAmount($amount)
            ->setVoucherUuid($voucherUuidString)
            ->setValid(true);

        $voucher = (new Voucher())
            ->setType(VoucherTypeEnum::CONCRETE)
            ->setDiscount(5.0);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => $voucherUuidString])
            ->willReturn($voucher);

        $this->orderRepository->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(
                    function (Order $order) use ($voucher): true {
                        $this->assertSame(94.9, $order->getAmount());
                        $this->assertEquals($voucher, $order->getVoucher());
                        $this->assertTrue(Uuid::isValid($order->getUuid()->toString()));
                        return true;
                    }
                )
            );

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.success.order.created_successfully')
            ->willReturnArgument(0);

        $actual = $this->orderService->create($requestData);
        $actualOrder = $actual->getOrder();

        $this->assertSame('response.success.order.created_successfully', $actual->getMessage());
        $this->assertSame(94.9, $actualOrder->getAmount());
        $this->assertEquals($voucher, $actualOrder->getVoucher());
        $this->assertTrue(Uuid::isValid($actualOrder->getUuid()->toString()));
    }

    public function testCreate(): void
    {
        $amount = 99.90;

        $requestData = OrderCreationRequestData::create()
            ->setAmount($amount)
            ->setValid(true);

        $this->orderRepository->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(
                    function (Order $order) use ($amount): true {
                        $this->assertSame($amount, $order->getAmount());
                        $this->assertTrue(Uuid::isValid($order->getUuid()->toString()));
                        return true;
                    }
                )
            );

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.success.order.created_successfully')
            ->willReturnArgument(0);

        $this->voucherRepository->expects($this->never())->method($this->anything());

        $actual = $this->orderService->create($requestData);
        $actualOrder = $actual->getOrder();

        $this->assertSame('response.success.order.created_successfully', $actual->getMessage());
        $this->assertSame($amount, $actualOrder->getAmount());
        $this->assertTrue(Uuid::isValid($actualOrder->getUuid()->toString()));
    }

    public function testListWithInvalidRequestData(): void
    {
        $requestData = OrderListRequestData::create();

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('exception.message.request_data_not_validated')
            ->willReturnArgument(0);

        $this->orderRepository->expects($this->never())->method($this->anything());

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('exception.message.request_data_not_validated');

        $this->orderService->list($requestData);
    }

    /**
     * @throws \ReflectionException
     */
    public function testList(): void
    {
        $pageSize = 3;
        $pageNumber = 1;

        $requestData = OrderListRequestData::create()
            ->setPageSize($pageSize)
            ->setPageNumber($pageNumber)
            ->setValid(true);

        $pagination = $this->createMock(PaginationInterface::class);

        $this->orderRepository->expects($this->once())
            ->method('findAllPaginated')
            ->with($pageNumber, $pageSize)
            ->willReturn($pagination);

        $uuid1 = Uuid::uuid4();

        $order1 = (new Order())
            ->setAmount(1.0)
            ->setVoucher(null);

        $this->setNonPublicPropertyValue($order1, 'uuid', $uuid1);
        $this->setNonPublicPropertyValue($order1, 'createdAt', new \DateTimeImmutable('2023-01-01'));
        $this->setNonPublicPropertyValue($order1, 'updatedAt', new \DateTimeImmutable('2023-01-01'));

        $uuid2 = Uuid::uuid4();

        $order2 = (new Order())
            ->setAmount(20.0)
            ->setVoucher(null);

        $this->setNonPublicPropertyValue($order2, 'uuid', $uuid2);
        $this->setNonPublicPropertyValue($order2, 'createdAt', new \DateTimeImmutable('2023-01-01'));
        $this->setNonPublicPropertyValue($order2, 'updatedAt', new \DateTimeImmutable('2023-01-01'));

        $uuid3 = Uuid::uuid4();
        $voucherUuid3 = Uuid::uuid4();

        $voucher3 = (new Voucher())
            ->setType(VoucherTypeEnum::PERCENTAGE)
            ->setExpirationDate(new \DateTimeImmutable('2022-06-12'))
            ->setDiscount(40.0);

        $this->setNonPublicPropertyValue($voucher3, 'uuid', $voucherUuid3);

        $order3 = (new Order())
            ->setAmount(99.90)
            ->setVoucher($voucher3);

        $this->setNonPublicPropertyValue($order3, 'uuid', $uuid3);
        $this->setNonPublicPropertyValue($order3, 'createdAt', new \DateTimeImmutable('2023-01-01'));
        $this->setNonPublicPropertyValue($order3, 'updatedAt', new \DateTimeImmutable('2023-01-01'));

        $items = new \ArrayObject([$order1, $order2, $order3]);
        $itemsCount = $items->count();

        $pagination->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $pagination->expects($this->once())
            ->method('getItemNumberPerPage')
            ->willReturn($pageSize);

        $pagination->expects($this->once())
            ->method('getTotalItemCount')
            ->willReturn($itemsCount);

        $pagination->expects($this->once())
            ->method('getCurrentPageNumber')
            ->willReturn($pageNumber);

        $this->translator->expects($this->never())->method($this->anything());

        $expected = OrderListDto::create()
            ->setList(
                [
                    [
                        'uuid' => $uuid1->toString(),
                        'amount' => 1.0,
                        'voucherUuid' => null,
                        'createdAt' => '2023-01-01 00:00:00',
                        'updatedAt' => '2023-01-01 00:00:00',
                    ],
                    [
                        'uuid' => $uuid2->toString(),
                        'amount' => 20.0,
                        'voucherUuid' => null,
                        'createdAt' => '2023-01-01 00:00:00',
                        'updatedAt' => '2023-01-01 00:00:00',
                    ],
                    [
                        'uuid' => $uuid3->toString(),
                        'amount' => 99.90,
                        'voucherUuid' => $voucherUuid3->toString(),
                        'createdAt' => '2023-01-01 00:00:00',
                        'updatedAt' => '2023-01-01 00:00:00',
                    ],
                ]
            )
            ->setPaginationResponseData(
                PaginationResponseData::create()
                    ->setPageNumber($pageNumber)
                    ->setPageSize($pageSize)
                    ->setTotalOfItems($itemsCount)
                    ->setNumberOfPages(1)
            );

        $this->assertEquals($expected, $this->orderService->list($requestData));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->voucherRepository = $this->createMock(VoucherRepositoryInterface::class);

        $this->orderService = new OrderService(
            $this->translator,
            $this->orderRepository,
            $this->voucherRepository,
        );
    }
}
