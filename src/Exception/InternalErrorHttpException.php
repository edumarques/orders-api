<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InternalErrorHttpException extends HttpException
{
    /**
     * @codeCoverageIgnore
     *
     * @param array<string, mixed> $headers
     *
     * @inheritDoc
     */
    public function __construct(string $message = '', \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct(Response::HTTP_INTERNAL_SERVER_ERROR, $message, $previous, $headers, $code);
    }
}
