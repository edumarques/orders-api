<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Entity\Voucher;
use App\Exception\ApiHttpExceptionInterface;
use App\Exception\InvalidRequestDataException;
use App\Repository\VoucherRepositoryInterface;
use App\Validator\VoucherRemovalRequestValidator;
use App\ValueObject\VoucherRemovalRequestData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class VoucherRemovalRequestValidatorTest extends TestCase
{
    private MockObject $translator;

    private MockObject $voucherRepository;

    private VoucherRemovalRequestValidator $voucherRemovalRequestValidator;

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithoutUuid(): void
    {
        $request = new Request();
        $requestData = VoucherRemovalRequestData::createFromRequest($request);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.voucher.uuid_must_be_provided_and_valid')
            ->willReturnArgument(0);

        $this->voucherRepository->expects($this->never())->method($this->anything());

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->voucherRemovalRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithInvalidUuid(): void
    {
        $uuid = 'uuid';
        $request = new Request();
        $requestData = VoucherRemovalRequestData::createFromRequest($request)->setUuid($uuid);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.voucher.uuid_must_be_provided_and_valid')
            ->willReturnArgument(0);

        $this->voucherRepository->expects($this->never())->method($this->anything());

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->voucherRemovalRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWhenVoucherIsNotFound(): void
    {
        $uuid = '905f1397-9f79-48a7-86e5-84e0ec4193be';
        $request = new Request();
        $requestData = VoucherRemovalRequestData::createFromRequest($request)->setUuid($uuid);

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

        $this->voucherRemovalRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidate(): void
    {
        $uuid = '905f1397-9f79-48a7-86e5-84e0ec4193be';
        $request = new Request();
        $requestData = VoucherRemovalRequestData::createFromRequest($request)->setUuid($uuid);

        $voucher = $this->createMock(Voucher::class);

        $this->voucherRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['uuid' => $uuid])
            ->willReturn($voucher);

        $this->translator->expects($this->never())->method($this->anything());

        $this->voucherRemovalRequestValidator->validate($requestData);

        $this->assertTrue($requestData->isValid());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->voucherRepository = $this->createMock(VoucherRepositoryInterface::class);

        $this->voucherRemovalRequestValidator = new VoucherRemovalRequestValidator(
            $this->translator,
            $this->voucherRepository,
        );
    }
}
