<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\OrderCreationDto;
use App\Dto\OrderListDto;
use App\Enum\VoucherEnum;
use App\Service\OrderServiceInterface;
use App\Validator\OrderCreationRequestValidator;
use App\Validator\OrderListRequestValidator;
use App\ValueObject\OrderCreationRequestData;
use App\ValueObject\OrderListRequestData;
use App\ValueObject\SuccessfulResponseData;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/orders', name: 'orders_')]
final class OrderController extends AbstractApiController
{
    public function __construct(
        private readonly OrderServiceInterface $orderService
    ) {
    }

    /**
     * Create an order
     *
     * @throws \Throwable
     */
    #[OA\Tag(name: 'Orders')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            required: ['amount'],
            properties: [
                new OA\Property(property: 'amount', type: 'float'),
                new OA\Property(property: 'voucherUuid', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Creates an order with the provided values',
        content: new OA\JsonContent(
            required: ['status', 'message', 'data', 'pagination'],
            properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(
                    property: 'data',
                    required: ['uuid'],
                    properties: [
                        new OA\Property(property: 'uuid', type: 'string'),
                    ],
                ),
                new OA\Property(property: 'pagination', default: null),
            ]
        )
    )]
    #[OA\Response(ref: '#/components/responses/BadRequest', response: Response::HTTP_BAD_REQUEST)]
    #[OA\Response(ref: '#/components/responses/MethodNotAllowed', response: Response::HTTP_METHOD_NOT_ALLOWED)]
    #[OA\Response(ref: '#/components/responses/InternalError', response: Response::HTTP_INTERNAL_SERVER_ERROR)]
    #[Route('', name: 'create', methods: [Request::METHOD_POST])]
    public function create(
        Request $request,
        OrderCreationRequestValidator $requestValidator
    ): JsonResponse {
        $requestData = OrderCreationRequestData::createFromRequest($request);

        $requestValidator->validate($requestData);

        /** @var OrderCreationDto $return */
        $return = $this->orderService->create($requestData);

        $responseData = SuccessfulResponseData::create()
            ->setStatusCode(Response::HTTP_CREATED)
            ->setMessage($return->getMessage())
            ->setData([VoucherEnum::UUID->value => $return->getOrder()->getUuid()->toString()]);

        return $this->jsonResponse($responseData);
    }

    /**
     * List orders
     *
     * @throws \Throwable
     */
    #[OA\Tag(name: 'Orders')]
    #[OA\Parameter(ref: '#/components/parameters/PageSize')]
    #[OA\Parameter(ref: '#/components/parameters/PageNumber')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns a paginated list of orders',
        content: new OA\JsonContent(
            required: ['status', 'message', 'data', 'pagination'],
            properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        required: ['uuid', 'amount', 'voucherUuid', 'createdAt', 'updatedAt'],
                        properties: [
                            new OA\Property(property: 'uuid', type: 'string'),
                            new OA\Property(property: 'amount', type: 'float'),
                            new OA\Property(property: 'voucherUuid', type: 'string'),
                            new OA\Property(property: 'createdAt', type: 'string'),
                            new OA\Property(property: 'updatedAt', type: 'string'),
                        ]
                    )
                ),
                new OA\Property(property: 'pagination', ref: '#/components/schemas/Pagination'),
            ]
        )
    )]
    #[OA\Response(ref: '#/components/responses/BadRequest', response: Response::HTTP_BAD_REQUEST)]
    #[OA\Response(ref: '#/components/responses/MethodNotAllowed', response: Response::HTTP_METHOD_NOT_ALLOWED)]
    #[OA\Response(ref: '#/components/responses/InternalError', response: Response::HTTP_INTERNAL_SERVER_ERROR)]
    #[Route('', name: 'list', methods: [Request::METHOD_GET])]
    public function list(
        Request $request,
        OrderListRequestValidator $requestValidator
    ): JsonResponse {
        $requestData = OrderListRequestData::createFromRequest($request);

        $requestValidator->validate($requestData);

        /** @var OrderListDto $return */
        $return = $this->orderService->list($requestData);

        $responseData = SuccessfulResponseData::create()
            ->setData($return->getList())
            ->setPaginationData($return->getPaginationResponseData());

        return $this->jsonResponse($responseData);
    }
}
