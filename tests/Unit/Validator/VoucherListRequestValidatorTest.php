<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Validator\VoucherListRequestValidator;
use App\ValueObject\VoucherListRequestData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class VoucherListRequestValidatorTest extends TestCase
{
    private MockObject $translator;

    private VoucherListRequestValidator $voucherListRequestValidator;

    public function testValidate(): void
    {
        $request = new Request();
        $requestData = VoucherListRequestData::createFromRequest($request);

        $this->translator->expects($this->never())->method($this->anything());

        $this->voucherListRequestValidator->validate($requestData);

        $this->assertTrue($requestData->isValid());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->voucherListRequestValidator = new VoucherListRequestValidator($this->translator);
    }
}
