<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Validator\OrderListRequestValidator;
use App\ValueObject\OrderListRequestData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderListRequestValidatorTest extends TestCase
{
    private MockObject $translator;

    private OrderListRequestValidator $orderListRequestValidator;

    public function testValidate(): void
    {
        $requestData = OrderListRequestData::create();

        $this->translator->expects($this->never())->method($this->anything());

        $this->orderListRequestValidator->validate($requestData);

        $this->assertTrue($requestData->isValid());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->orderListRequestValidator = new OrderListRequestValidator($this->translator);
    }
}
