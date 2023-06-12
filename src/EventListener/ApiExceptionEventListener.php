<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\ApiHttpExceptionInterface;
use App\Util\EnvironmentUtil;
use App\ValueObject\FailedResponseData;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ApiExceptionEventListener
{
    public function __construct(
        protected TranslatorInterface $translator,
        protected EnvironmentUtil $environmentUtil
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $isProd = $this->environmentUtil->isProdEnvironment();

        $throwable = $event->getThrowable();

        $globalExceptionMessage = $this->translator->trans(
            $isProd ? 'exception.message.global' : 'exception.message.global_detailed',
            ['%error_message%' => $throwable->getMessage()]
        );

        $jsonMessage = $throwable instanceof ApiHttpExceptionInterface ?
            $throwable->getJsonMessage() ?? $globalExceptionMessage
            : $globalExceptionMessage;

        $exceptionMessage = $throwable::class;

        $exceptionMessage = ($originalExceptionMessage = $throwable->getMessage()) !== '' ?
            sprintf('%s: %s', $exceptionMessage, $originalExceptionMessage)
            : $exceptionMessage;

        $trace = array_map(
            static fn(array $element): array => [
                'function' => $element['function'],
                'class' => $element['class'] ?? null,
                'line' => $element['line'] ?? null,
            ],
            $throwable->getTrace()
        );

        $responseData = FailedResponseData::create()
            ->setMessage($jsonMessage)
            ->setException($exceptionMessage)
            ->setExceptionTrace($trace);

        $statusCode = method_exists($throwable, 'getStatusCode')
            ? $throwable->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        $event->setResponse(
            new JsonResponse(
                $responseData->toArray(!$isProd),
                $statusCode
            )
        );
    }
}
