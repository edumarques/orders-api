<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Entity\Order;
use App\Entity\Voucher;
use App\Exception\ApiHttpExceptionInterface;
use App\Exception\InvalidRequestDataException;
use App\Repository\VoucherRepositoryInterface;
use App\Validator\VoucherUpdateRequestValidator;
use App\ValueObject\VoucherUpdateRequestData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class VoucherUpdateRequestValidatorTest extends TestCase
{
    private MockObject $translator;

    private MockObject $voucherRepository;

    private VoucherUpdateRequestValidator $voucherUpdateRequestValidator;

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithoutUuid(): void
    {
        $request = new Request();
        $requestData = VoucherUpdateRequestData::createFromRequest($request);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.voucher.uuid_must_be_provided_and_valid')
            ->willReturnArgument(0);

        $this->voucherRepository->expects($this->never())->method($this->anything());

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->voucherUpdateRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithInvalidUuid(): void
    {
        $uuid = 'uuid';
        $request = new Request();
        $requestData = VoucherUpdateRequestData::createFromRequest($request)->setUuid($uuid);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.voucher.uuid_must_be_provided_and_valid')
            ->willReturnArgument(0);

        $this->voucherRepository->expects($this->never())->method($this->anything());

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->voucherUpdateRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWhenVoucherIsNotFound(): void
    {
        $uuid = '905f1397-9f79-48a7-86e5-84e0ec4193be';
        $request = new Request();
        $requestData = VoucherUpdateRequestData::createFromRequest($request)->setUuid($uuid);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => $uuid])
            ->willReturn(null);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.voucher.not_found_for_provided_uuid')
            ->willReturnArgument(0);

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->voucherUpdateRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWhenVoucherIsNotActive(): void
    {
        $uuid = '905f1397-9f79-48a7-86e5-84e0ec4193be';
        $request = new Request();
        $requestData = VoucherUpdateRequestData::createFromRequest($request)->setUuid($uuid);

        $order = new Order();

        $voucher = (new Voucher())
            ->setOrder($order)
            ->setExpirationDate(null);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => $uuid])
            ->willReturn($voucher);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.voucher.must_be_active_to_be_updated')
            ->willReturnArgument(0);

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->voucherUpdateRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithoutValues(): void
    {
        $uuid = '905f1397-9f79-48a7-86e5-84e0ec4193be';
        $request = new Request();
        $requestData = VoucherUpdateRequestData::createFromRequest($request)->setUuid($uuid);

        $voucher = (new Voucher())
            ->setOrder(null)
            ->setExpirationDate(null);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => $uuid])
            ->willReturn($voucher);

        $this->translator->expects($this->never())->method($this->anything());

        $this->voucherUpdateRequestValidator->validate($requestData);

        $this->assertTrue($requestData->isValid());
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithInvalidType(): void
    {
        $uuid = '905f1397-9f79-48a7-86e5-84e0ec4193be';
        $request = new Request(content: '{"type":"REGULAR"}');
        $requestData = VoucherUpdateRequestData::createFromRequest($request)->setUuid($uuid);

        $voucher = (new Voucher())
            ->setOrder(null)
            ->setExpirationDate(null);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => $uuid])
            ->willReturn($voucher);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.voucher.type_must_be_valid')
            ->willReturnArgument(0);

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->voucherUpdateRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithInvalidDiscount(): void
    {
        $uuid = '905f1397-9f79-48a7-86e5-84e0ec4193be';
        $request = new Request(content: '{"type":"PERCENTAGE", "discount":5}');
        $requestData = VoucherUpdateRequestData::createFromRequest($request)->setUuid($uuid);

        $voucher = (new Voucher())
            ->setOrder(null)
            ->setExpirationDate(null);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => $uuid])
            ->willReturn($voucher);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.voucher.discount_must_be_valid')
            ->willReturnArgument(0);

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->voucherUpdateRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithInvalidExpirationDate(): void
    {
        $uuid = '905f1397-9f79-48a7-86e5-84e0ec4193be';
        $request = new Request(content: '{"type":"PERCENTAGE", "discount":5.0, "expirationDate":"2023-12"}');
        $requestData = VoucherUpdateRequestData::createFromRequest($request)->setUuid($uuid);

        $voucher = (new Voucher())
            ->setOrder(null)
            ->setExpirationDate(null);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => $uuid])
            ->willReturn($voucher);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.voucher.expiration_date_must_have_a_valid_format')
            ->willReturnArgument(0);

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->voucherUpdateRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidate(): void
    {
        $uuid = '905f1397-9f79-48a7-86e5-84e0ec4193be';
        $request = new Request(content: '{"type":"PERCENTAGE", "discount":5.0, "expirationDate":"2023-12-01"}');
        $requestData = VoucherUpdateRequestData::createFromRequest($request)->setUuid($uuid);

        $voucher = (new Voucher())
            ->setOrder(null)
            ->setExpirationDate(new \DateTimeImmutable());

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => $uuid])
            ->willReturn($voucher);

        $this->translator->expects($this->never())->method($this->anything());

        $this->voucherUpdateRequestValidator->validate($requestData);

        $this->assertTrue($requestData->isValid());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->voucherRepository = $this->createMock(VoucherRepositoryInterface::class);

        $this->voucherUpdateRequestValidator = new VoucherUpdateRequestValidator(
            $this->translator,
            $this->voucherRepository,
        );
    }
}
