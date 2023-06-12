<?php

declare(strict_types=1);

namespace App\Controller;

use App\ValueObject\ResponseDataInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiController extends BaseAbstractController
{
    protected function jsonResponse(ResponseDataInterface $responseData): JsonResponse
    {
        return $this->json(
            $responseData->toArray(),
            $responseData->getStatusCode() ?? Response::HTTP_OK
        );
    }
}
