<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Entity\Voucher;
use App\Exception\ApiHttpExceptionInterface;
use App\Exception\InvalidRequestDataException;
use App\Repository\VoucherRepositoryInterface;
use App\Validator\OrderCreationRequestValidator;
use App\ValueObject\OrderCreationRequestData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderCreationRequestValidatorTest extends TestCase
{
    private MockObject $translator;

    private MockObject $voucherRepository;

    private OrderCreationRequestValidator $orderCreationRequestValidator;

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithoutAmount(): void
    {
        $request = new Request();
        $requestData = OrderCreationRequestData::createFromRequest($request);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.order.amount_must_be_provided_and_valid')
            ->willReturnArgument(0);

        $this->voucherRepository->expects($this->never())->method($this->anything());

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->orderCreationRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithInvalidAmount(): void
    {
        $request = new Request(content: '{"amount":"c"}');
        $requestData = OrderCreationRequestData::createFromRequest($request);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.order.amount_must_be_provided_and_valid')
            ->willReturnArgument(0);

        $this->voucherRepository->expects($this->never())->method($this->anything());

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->orderCreationRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithNegativeAmount(): void
    {
        $request = new Request(content: '{"amount":-1}');
        $requestData = OrderCreationRequestData::createFromRequest($request);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.order.amount_must_be_provided_and_valid')
            ->willReturnArgument(0);

        $this->voucherRepository->expects($this->never())->method($this->anything());

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->orderCreationRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithoutVoucherUuid(): void
    {
        $request = new Request(content: '{"amount":1.0}');
        $requestData = OrderCreationRequestData::createFromRequest($request);

        $this->translator->expects($this->never())->method($this->anything());
        $this->voucherRepository->expects($this->never())->method($this->anything());

        $this->orderCreationRequestValidator->validate($requestData);

        $this->assertTrue($requestData->isValid());
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithInvalidVoucherUuid(): void
    {
        $request = new Request(content: '{"amount":1.0, "voucherUuid":"uuid"}');
        $requestData = OrderCreationRequestData::createFromRequest($request);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.order.voucher_uuid_is_not_valid')
            ->willReturnArgument(0);

        $this->voucherRepository->expects($this->never())->method($this->anything());

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->orderCreationRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWhenVoucherIsNotFound(): void
    {
        $request = new Request(content: '{"amount":1.0, "voucherUuid":"905f1397-9f79-48a7-86e5-84e0ec4193be"}');
        $requestData = OrderCreationRequestData::createFromRequest($request);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => '905f1397-9f79-48a7-86e5-84e0ec4193be'])
            ->willReturn(null);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.order.voucher_not_found')
            ->willReturnArgument(0);

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->orderCreationRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWhenVoucherIsUsed(): void
    {
        $request = new Request(content: '{"amount":1.0, "voucherUuid":"905f1397-9f79-48a7-86e5-84e0ec4193be"}');
        $requestData = OrderCreationRequestData::createFromRequest($request);

        $voucher = $this->createMock(Voucher::class);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => '905f1397-9f79-48a7-86e5-84e0ec4193be'])
            ->willReturn($voucher);

        $voucher->expects($this->once())
            ->method('isUsed')
            ->willReturn(true);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.order.voucher_already_used')
            ->willReturnArgument(0);

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->orderCreationRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWhenVoucherIsExpired(): void
    {
        $request = new Request(content: '{"amount":1.0, "voucherUuid":"905f1397-9f79-48a7-86e5-84e0ec4193be"}');
        $requestData = OrderCreationRequestData::createFromRequest($request);

        $voucher = $this->createMock(Voucher::class);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => '905f1397-9f79-48a7-86e5-84e0ec4193be'])
            ->willReturn($voucher);

        $voucher->expects($this->once())
            ->method('isUsed')
            ->willReturn(false);

        $voucher->expects($this->once())
            ->method('isExpired')
            ->willReturn(true);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.order.voucher_expired')
            ->willReturnArgument(0);

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->orderCreationRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidate(): void
    {
        $request = new Request(content: '{"amount":1.0, "voucherUuid":"905f1397-9f79-48a7-86e5-84e0ec4193be"}');
        $requestData = OrderCreationRequestData::createFromRequest($request);

        $voucher = $this->createMock(Voucher::class);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => '905f1397-9f79-48a7-86e5-84e0ec4193be'])
            ->willReturn($voucher);

        $voucher->expects($this->once())
            ->method('isUsed')
            ->willReturn(false);

        $voucher->expects($this->once())
            ->method('isExpired')
            ->willReturn(false);

        $this->translator->expects($this->never())->method($this->anything());

        $this->orderCreationRequestValidator->validate($requestData);

        $this->assertTrue($requestData->isValid());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->voucherRepository = $this->createMock(VoucherRepositoryInterface::class);

        $this->orderCreationRequestValidator = new OrderCreationRequestValidator(
            $this->translator,
            $this->voucherRepository
        );
    }
}
