<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidRequestDataException extends BadRequestHttpException implements ApiHttpExceptionInterface
{
    use HttpExceptionTrait;
}
