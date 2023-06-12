<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Exception\ApiHttpExceptionInterface;
use App\Exception\InvalidRequestDataException;
use App\Validator\VoucherCreationRequestValidator;
use App\ValueObject\VoucherCreationRequestData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class VoucherCreationRequestValidatorTest extends TestCase
{
    private MockObject $translator;

    private VoucherCreationRequestValidator $voucherCreationRequestValidator;

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithoutType(): void
    {
        $request = new Request();
        $requestData = VoucherCreationRequestData::createFromRequest($request);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.voucher.type_must_be_provided_and_valid')
            ->willReturnArgument(0);

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->voucherCreationRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithInvalidType(): void
    {
        $request = new Request(content: '{"type":"TYPE"');
        $requestData = VoucherCreationRequestData::createFromRequest($request);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.voucher.type_must_be_provided_and_valid')
            ->willReturnArgument(0);

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->voucherCreationRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithInvalidExpirationDate(): void
    {
        $request = new Request(content: '{"type":"CONCRETE", "expirationDate":"2023-12"}');
        $requestData = VoucherCreationRequestData::createFromRequest($request);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.voucher.expiration_date_must_have_a_valid_format')
            ->willReturnArgument(0);

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->voucherCreationRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithoutDiscount(): void
    {
        $request = new Request(content: '{"type":"CONCRETE", "expirationDate":"2023-12-12"}');
        $requestData = VoucherCreationRequestData::createFromRequest($request);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.voucher.discount_must_be_provided_and_valid')
            ->willReturnArgument(0);

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->voucherCreationRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidateWithInvalidDiscount(): void
    {
        $request = new Request(content: '{"type":"CONCRETE", "expirationDate":"2023-12-12", "discount":"c"}');
        $requestData = VoucherCreationRequestData::createFromRequest($request);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('response.error.validation.voucher.discount_must_be_provided_and_valid')
            ->willReturnArgument(0);

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('');

        $this->voucherCreationRequestValidator->validate($requestData);
    }

    /**
     * @throws ApiHttpExceptionInterface
     */
    public function testValidate(): void
    {
        $request = new Request(content: '{"type":"CONCRETE", "expirationDate":"2023-12-12", "discount":"10"}');
        $requestData = VoucherCreationRequestData::createFromRequest($request);

        $this->translator->expects($this->never())->method($this->anything());

        $this->voucherCreationRequestValidator->validate($requestData);

        $this->assertTrue($requestData->isValid());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->voucherCreationRequestValidator = new VoucherCreationRequestValidator($this->translator);
    }
}
