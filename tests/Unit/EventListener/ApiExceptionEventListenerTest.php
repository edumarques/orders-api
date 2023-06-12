<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventListener;

use App\Enum\ResponseStatusEnum;
use App\EventListener\ApiExceptionEventListener;
use App\Exception\ApiHttpExceptionInterface;
use App\Exception\HttpExceptionTrait;
use App\Tests\TestHelperTrait;
use App\Util\EnvironmentUtil;
use App\ValueObject\FailedResponseData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ApiExceptionEventListenerTest extends TestCase
{
    use TestHelperTrait;

    private MockObject $translator;

    private MockObject $environmentUtil;

    private ApiExceptionEventListener $apiExceptionEventListener;

    /**
     * @throws \ReflectionException
     */
    public function testOnKernelExceptionWithGenericExceptionInProdEnvironment(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);
        $requestType = rand(1, 999999);
        $throwable = new \Exception('exception message');
        $this->setNonPublicPropertyValue($throwable, 'trace', [
            [
                'function' => null,
            ],
            [
                'file' => 'Component.php',
                'line' => 100,
                'class' => 'Component',
                'function' => 'run',
            ],
        ]);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('exception.message.global', ['%error_message%' => 'exception message'])
            ->willReturnArgument(0);

        $this->environmentUtil->expects($this->once())
            ->method('isProdEnvironment')
            ->willReturn(true);

        $event = new ExceptionEvent($kernel, $request, $requestType, $throwable);

        $this->apiExceptionEventListener->onKernelException($event);

        $expectedResponse = new JsonResponse(
            FailedResponseData::create()
                ->setMessage('exception.message.global')
                ->toArray(false),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );

        $this->assertEquals($expectedResponse, $event->getResponse());
    }

    /**
     * @throws \ReflectionException
     */
    public function testOnKernelExceptionWithGenericExceptionInNonProdEnvironment(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);
        $requestType = rand(1, 999999);
        $throwable = new \Exception('exception message');
        $this->setNonPublicPropertyValue($throwable, 'trace', [
            [
                'function' => null,
            ],
            [
                'file' => 'Component.php',
                'line' => 100,
                'class' => 'Component',
                'function' => 'run',
            ],
        ]);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('exception.message.global_detailed', ['%error_message%' => 'exception message'])
            ->willReturnArgument(0);

        $this->environmentUtil->expects($this->once())
            ->method('isProdEnvironment')
            ->willReturn(false);

        $event = new ExceptionEvent($kernel, $request, $requestType, $throwable);

        $this->apiExceptionEventListener->onKernelException($event);

        $expectedResponse = new JsonResponse(
            FailedResponseData::create()
                ->setMessage('exception.message.global_detailed')
                ->setException('Exception: exception message')
                ->setExceptionTrace([
                    [
                        'function' => null,
                        'class' => null,
                        'line' => null,
                    ],
                    [
                        'function' => 'run',
                        'class' => 'Component',
                        'line' => 100,
                    ],
                ])
                ->toArray(),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );

        $this->assertEquals($expectedResponse, $event->getResponse());
    }

    public function testOnKernelExceptionWithHttpExceptionInProdEnvironment(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);
        $requestType = rand(1, 999999);
        $exceptionClass = new class extends BadRequestHttpException implements ApiHttpExceptionInterface {
            use HttpExceptionTrait;
        };
        $throwable = new $exceptionClass('exception message');

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('exception.message.global', ['%error_message%' => 'exception message'])
            ->willReturnArgument(0);

        $this->environmentUtil->expects($this->once())
            ->method('isProdEnvironment')
            ->willReturn(true);

        $event = new ExceptionEvent($kernel, $request, $requestType, $throwable);

        $this->apiExceptionEventListener->onKernelException($event);

        $expectedResponse = new JsonResponse(
            FailedResponseData::create()
                ->setMessage('exception.message.global')
                ->toArray(false),
            Response::HTTP_BAD_REQUEST
        );

        $this->assertEquals($expectedResponse, $event->getResponse());
    }

    public function testOnKernelExceptionWithHttpExceptionInNonProdEnvironment(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);
        $requestType = rand(1, 999999);
        $exceptionClass = new class extends NotFoundHttpException implements ApiHttpExceptionInterface {
            use HttpExceptionTrait;
        };
        $throwable = (new $exceptionClass())->setJsonMessage('json message');

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('exception.message.global_detailed', ['%error_message%' => ''])
            ->willReturnArgument(0);

        $this->environmentUtil->expects($this->once())
            ->method('isProdEnvironment')
            ->willReturn(false);

        $event = new ExceptionEvent($kernel, $request, $requestType, $throwable);

        $this->apiExceptionEventListener->onKernelException($event);

        $actualResponse = $event->getResponse();
        $actualResponseContentDecoded = json_decode($actualResponse->getContent());

        $this->assertInstanceOf(JsonResponse::class, $actualResponse);
        $this->assertSame(ResponseStatusEnum::STATUS_ERROR->value, $actualResponseContentDecoded->status);
        $this->assertSame('json message', $actualResponseContentDecoded->message);
        $this->assertStringContainsString(
            'ExceptionEventListenerTest',
            $actualResponseContentDecoded->exception
        );
        $this->assertNull($actualResponseContentDecoded->data);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->environmentUtil = $this->createMock(EnvironmentUtil::class);

        $this->apiExceptionEventListener = new ApiExceptionEventListener(
            $this->translator,
            $this->environmentUtil,
        );
    }
}
