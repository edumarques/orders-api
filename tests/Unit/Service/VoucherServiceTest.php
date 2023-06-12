<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Dto\VoucherListDto;
use App\Dto\VoucherRemovalDto;
use App\Entity\Order;
use App\Entity\Voucher;
use App\Enum\VoucherStatusEnum;
use App\Enum\VoucherTypeEnum;
use App\Exception\InvalidRequestDataException;
use App\Repository\VoucherRepositoryInterface;
use App\Service\VoucherService;
use App\Tests\TestHelperTrait;
use App\ValueObject\PaginationResponseData;
use App\ValueObject\VoucherCreationRequestData;
use App\ValueObject\VoucherListRequestData;
use App\ValueObject\VoucherRemovalRequestData;
use App\ValueObject\VoucherUpdateRequestData;
use Knp\Component\Pager\Pagination\PaginationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

final class VoucherServiceTest extends TestCase
{
    use TestHelperTrait;

    private MockObject $translator;

    private MockObject $voucherRepository;

    private VoucherService $voucherService;

    public function testCreateWithInvalidRequestData(): void
    {
        $requestData = VoucherCreationRequestData::create();

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('exception.message.request_data_not_validated')
            ->willReturnArgument(0);

        $this->voucherRepository->expects($this->never())->method($this->anything());

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('exception.message.request_data_not_validated');

        $this->voucherService->create($requestData);
    }

    public function testCreate(): void
    {
        $type = VoucherTypeEnum::CONCRETE;
        $expirationDate = new \DateTimeImmutable();
        $discount = 5.0;

        $requestData = VoucherCreationRequestData::create()
            ->setType($type)
            ->setExpirationDate($expirationDate)
            ->setDiscount($discount)
            ->setValid(true);

        $this->voucherRepository->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(
                    function (Voucher $voucher) use ($type, $expirationDate, $discount): true {
                        $this->assertSame($type, $voucher->getType());
                        $this->assertSame($expirationDate, $voucher->getExpirationDate());
                        $this->assertSame($discount, $voucher->getDiscount());
                        $this->assertTrue(Uuid::isValid($voucher->getUuid()->toString()));
                        return true;
                    }
                )
            );

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.success.voucher.created_successfully')
            ->willReturnArgument(0);

        $actual = $this->voucherService->create($requestData);
        $actualVoucher = $actual->getVoucher();

        $this->assertSame('response.success.voucher.created_successfully', $actual->getMessage());
        $this->assertSame($type, $actualVoucher->getType());
        $this->assertSame($expirationDate, $actualVoucher->getExpirationDate());
        $this->assertSame($discount, $actualVoucher->getDiscount());
        $this->assertTrue(Uuid::isValid($actualVoucher->getUuid()->toString()));
    }

    public function testListWithInvalidRequestData(): void
    {
        $requestData = VoucherListRequestData::create();

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('exception.message.request_data_not_validated')
            ->willReturnArgument(0);

        $this->voucherRepository->expects($this->never())->method($this->anything());

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('exception.message.request_data_not_validated');

        $this->voucherService->list($requestData);
    }

    /**
     * @throws \ReflectionException
     */
    public function testList(): void
    {
        $status = VoucherStatusEnum::ACTIVE;
        $pageSize = 3;
        $pageNumber = 1;

        $requestData = VoucherListRequestData::create()
            ->setStatus($status)
            ->setPageSize($pageSize)
            ->setPageNumber($pageNumber)
            ->setValid(true);

        $pagination = $this->createMock(PaginationInterface::class);

        $this->voucherRepository->expects($this->once())
            ->method('findAllPaginated')
            ->with($status, $pageNumber, $pageSize)
            ->willReturn($pagination);

        $uuid1 = Uuid::uuid4();

        $voucher1 = (new Voucher())
            ->setOrder(null)
            ->setType(VoucherTypeEnum::PERCENTAGE)
            ->setExpirationDate(new \DateTimeImmutable('2023-05-12'))
            ->setDiscount(1.0);

        $this->setNonPublicPropertyValue($voucher1, 'uuid', $uuid1);
        $this->setNonPublicPropertyValue($voucher1, 'createdAt', new \DateTimeImmutable('2023-01-01'));
        $this->setNonPublicPropertyValue($voucher1, 'updatedAt', new \DateTimeImmutable('2023-01-01'));

        $uuid2 = Uuid::uuid4();

        $voucher2 = (new Voucher())
            ->setOrder(null)
            ->setType(VoucherTypeEnum::CONCRETE)
            ->setExpirationDate(new \DateTimeImmutable('2023-06-13'))
            ->setDiscount(20.0);

        $this->setNonPublicPropertyValue($voucher2, 'uuid', $uuid2);
        $this->setNonPublicPropertyValue($voucher2, 'createdAt', new \DateTimeImmutable('2023-01-01'));
        $this->setNonPublicPropertyValue($voucher2, 'updatedAt', new \DateTimeImmutable('2023-01-01'));

        $uuid3 = Uuid::uuid4();
        $orderUuid3 = Uuid::uuid4();

        $order3 = new Order();

        $this->setNonPublicPropertyValue($order3, 'uuid', $orderUuid3);

        $voucher3 = (new Voucher())
            ->setOrder($order3)
            ->setType(VoucherTypeEnum::PERCENTAGE)
            ->setExpirationDate(new \DateTimeImmutable('2022-06-12'))
            ->setDiscount(40.0);

        $this->setNonPublicPropertyValue($voucher3, 'uuid', $uuid3);
        $this->setNonPublicPropertyValue($voucher3, 'createdAt', new \DateTimeImmutable('2023-01-01'));
        $this->setNonPublicPropertyValue($voucher3, 'updatedAt', new \DateTimeImmutable('2023-01-01'));

        $items = new \ArrayObject([$voucher1, $voucher2, $voucher3]);
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

        $expected = VoucherListDto::create()
            ->setList(
                [
                    [
                        'uuid' => $uuid1->toString(),
                        'type' => VoucherTypeEnum::PERCENTAGE->value,
                        'discount' => 1.0,
                        'status' => VoucherStatusEnum::EXPIRED->value,
                        'orderUuid' => null,
                        'expirationDate' => '2023-05-12',
                        'createdAt' => '2023-01-01 00:00:00',
                        'updatedAt' => '2023-01-01 00:00:00',
                    ],
                    [
                        'uuid' => $uuid2->toString(),
                        'type' => VoucherTypeEnum::CONCRETE->value,
                        'discount' => 20.0,
                        'status' => VoucherStatusEnum::ACTIVE->value,
                        'orderUuid' => null,
                        'expirationDate' => '2023-06-13',
                        'createdAt' => '2023-01-01 00:00:00',
                        'updatedAt' => '2023-01-01 00:00:00',
                    ],
                    [
                        'uuid' => $uuid3->toString(),
                        'type' => VoucherTypeEnum::PERCENTAGE->value,
                        'discount' => 40.0,
                        'status' => VoucherStatusEnum::USED->value,
                        'orderUuid' => $orderUuid3->toString(),
                        'expirationDate' => '2022-06-12',
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

        $this->assertEquals($expected, $this->voucherService->list($requestData));
    }

    public function testUpdateWithInvalidRequestData(): void
    {
        $requestData = VoucherUpdateRequestData::create();

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('exception.message.request_data_not_validated')
            ->willReturnArgument(0);

        $this->voucherRepository->expects($this->never())->method($this->anything());

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('exception.message.request_data_not_validated');

        $this->voucherService->update($requestData);
    }

    /**
     * @throws \ReflectionException
     */
    public function testUpdateWithAllValues(): void
    {
        $uuid = Uuid::uuid4();
        $uuidString = $uuid->toString();
        $type = VoucherTypeEnum::CONCRETE;
        $expirationDate = new \DateTimeImmutable();
        $discount = 5.0;

        $requestData = VoucherUpdateRequestData::create()
            ->setUuid($uuidString)
            ->setType($type)
            ->setExpirationDate($expirationDate)
            ->setDiscount($discount)
            ->setValid(true);

        $voucher = (new Voucher())
            ->setType(VoucherTypeEnum::PERCENTAGE)
            ->setExpirationDate($expirationDate->modify('-1 month'))
            ->setDiscount(1.0);

        $this->setNonPublicPropertyValue($voucher, 'uuid', $uuid);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => $uuidString])
            ->willReturn($voucher);

        $this->voucherRepository->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(
                    function (Voucher $voucher) use ($uuid, $type, $expirationDate, $discount): true {
                        $this->assertEquals($uuid, $voucher->getUuid());
                        $this->assertSame($type, $voucher->getType());
                        $this->assertSame($expirationDate, $voucher->getExpirationDate());
                        $this->assertSame($discount, $voucher->getDiscount());
                        $this->assertTrue(Uuid::isValid($voucher->getUuid()->toString()));
                        return true;
                    }
                )
            );

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.success.voucher.updated_successfully')
            ->willReturnArgument(0);

        $actual = $this->voucherService->update($requestData);
        $actualVoucher = $actual->getVoucher();

        $this->assertSame('response.success.voucher.updated_successfully', $actual->getMessage());
        $this->assertEquals($uuid, $actualVoucher->getUuid());
        $this->assertSame($type, $actualVoucher->getType());
        $this->assertSame($expirationDate, $actualVoucher->getExpirationDate());
        $this->assertSame($discount, $actualVoucher->getDiscount());
        $this->assertTrue(Uuid::isValid($actualVoucher->getUuid()->toString()));
    }

    /**
     * @throws \ReflectionException
     */
    public function testUpdateWithoutValues(): void
    {
        $uuid = Uuid::uuid4();
        $uuidString = $uuid->toString();
        $type = VoucherTypeEnum::CONCRETE;
        $expirationDate = new \DateTimeImmutable();
        $discount = 5.0;

        $requestData = VoucherUpdateRequestData::create()
            ->setUuid($uuidString)
            ->setType(null)
            ->setExpirationDate(null)
            ->setDiscount(null)
            ->setValid(true);

        $voucher = (new Voucher())
            ->setType($type)
            ->setExpirationDate($expirationDate)
            ->setDiscount($discount);

        $this->setNonPublicPropertyValue($voucher, 'uuid', $uuid);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => $uuidString])
            ->willReturn($voucher);

        $this->voucherRepository->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(
                    function (Voucher $voucher) use ($uuid, $type, $expirationDate, $discount): true {
                        $this->assertEquals($uuid, $voucher->getUuid());
                        $this->assertSame($type, $voucher->getType());
                        $this->assertSame($expirationDate, $voucher->getExpirationDate());
                        $this->assertSame($discount, $voucher->getDiscount());
                        $this->assertTrue(Uuid::isValid($voucher->getUuid()->toString()));
                        return true;
                    }
                )
            );

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.success.voucher.updated_successfully')
            ->willReturnArgument(0);

        $actual = $this->voucherService->update($requestData);
        $actualVoucher = $actual->getVoucher();

        $this->assertSame('response.success.voucher.updated_successfully', $actual->getMessage());
        $this->assertEquals($uuid, $actualVoucher->getUuid());
        $this->assertSame($type, $actualVoucher->getType());
        $this->assertSame($expirationDate, $actualVoucher->getExpirationDate());
        $this->assertSame($discount, $actualVoucher->getDiscount());
        $this->assertTrue(Uuid::isValid($actualVoucher->getUuid()->toString()));
    }

    public function testDeleteWithInvalidRequestData(): void
    {
        $requestData = VoucherRemovalRequestData::create();

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('exception.message.request_data_not_validated')
            ->willReturnArgument(0);

        $this->voucherRepository->expects($this->never())->method($this->anything());

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('exception.message.request_data_not_validated');

        $this->voucherService->delete($requestData);
    }

    public function testDelete(): void
    {
        $uuid = Uuid::uuid4();
        $uuidString = $uuid->toString();

        $requestData = VoucherRemovalRequestData::create()
            ->setUuid($uuidString)
            ->setValid(true);

        $voucher = $this->createMock(Voucher::class);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => $uuidString])
            ->willReturn($voucher);

        $this->voucherRepository->expects($this->once())
            ->method('delete')
            ->with($voucher);

        $voucher->expects($this->once())
            ->method('getUuid')
            ->willReturn($uuid);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.success.voucher.removed_successfully')
            ->willReturnArgument(0);

        $expected = VoucherRemovalDto::create()
            ->setMessage('response.success.voucher.removed_successfully')
            ->setUuid($uuid);

        $this->assertEquals($expected, $this->voucherService->delete($requestData));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->voucherRepository = $this->createMock(VoucherRepositoryInterface::class);

        $this->voucherService = new VoucherService(
            $this->translator,
            $this->voucherRepository,
        );
    }
}
