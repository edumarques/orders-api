<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\VoucherCreationDto;
use App\Dto\VoucherListDto;
use App\Dto\VoucherRemovalDto;
use App\Dto\VoucherUpdateDto;
use App\Enum\VoucherEnum;
use App\Service\VoucherServiceInterface;
use App\Validator\VoucherCreationRequestValidator;
use App\Validator\VoucherListRequestValidator;
use App\Validator\VoucherRemovalRequestValidator;
use App\Validator\VoucherUpdateRequestValidator;
use App\ValueObject\SuccessfulResponseData;
use App\ValueObject\VoucherCreationRequestData;
use App\ValueObject\VoucherListRequestData;
use App\ValueObject\VoucherRemovalRequestData;
use App\ValueObject\VoucherUpdateRequestData;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vouchers', name: 'vouchers_')]
final class VoucherController extends AbstractApiController
{
    public function __construct(
        private readonly VoucherServiceInterface $voucherService
    ) {
    }

    /**
     * Create a voucher
     *
     * @throws \Throwable
     */
    #[OA\Tag(name: 'Vouchers')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            required: ['type', 'discount'],
            properties: [
                new OA\Property(property: 'type', type: 'string', enum: ['CONCRETE', 'PERCENTAGE']),
                new OA\Property(property: 'discount', type: 'float'),
                new OA\Property(property: 'expirationDate', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Creates a voucher with the provided values',
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
        VoucherCreationRequestValidator $requestValidator
    ): JsonResponse {
        $requestData = VoucherCreationRequestData::createFromRequest($request);

        $requestValidator->validate($requestData);

        /** @var VoucherCreationDto $return */
        $return = $this->voucherService->create($requestData);

        $responseData = SuccessfulResponseData::create()
            ->setStatusCode(Response::HTTP_CREATED)
            ->setMessage($return->getMessage())
            ->setData([VoucherEnum::UUID->value => $return->getVoucher()->getUuid()->toString()]);

        return $this->jsonResponse($responseData);
    }

    /**
     * List vouchers
     *
     * @throws \Throwable
     */
    #[OA\Tag(name: 'Vouchers')]
    #[OA\Parameter(ref: '#/components/parameters/PageSize')]
    #[OA\Parameter(ref: '#/components/parameters/PageNumber')]
    #[OA\Parameter(
        name: 'status',
        description: 'expired (0); active (1); used (2);',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns a paginated list of vouchers',
        content: new OA\JsonContent(
            required: ['status', 'message', 'data', 'pagination'],
            properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        required: [
                            'uuid',
                            'type',
                            'discount',
                            'status',
                            'orderUuid',
                            'expirationDate',
                            'createdAt',
                            'updatedAt',
                        ],
                        properties: [
                            new OA\Property(property: 'uuid', type: 'string'),
                            new OA\Property(property: 'type', type: 'string', enum: ['CONCRETE', 'PERCENTAGE']),
                            new OA\Property(property: 'discount', type: 'float'),
                            new OA\Property(
                                property: 'status',
                                description: 'expired (0); active (1); used (2);',
                                type: 'integer',
                                enum: [0, 1, 2]
                            ),
                            new OA\Property(property: 'orderUuid', type: 'string'),
                            new OA\Property(property: 'expirationDate', type: 'string'),
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
        VoucherListRequestValidator $requestValidator
    ): JsonResponse {
        $requestData = VoucherListRequestData::createFromRequest($request);

        $requestValidator->validate($requestData);

        /** @var VoucherListDto $return */
        $return = $this->voucherService->list($requestData);

        $responseData = SuccessfulResponseData::create()
            ->setData($return->getList())
            ->setPaginationData($return->getPaginationResponseData());

        return $this->jsonResponse($responseData);
    }

    /**
     * Updates a voucher
     *
     * @throws \Throwable
     */
    #[OA\Tag(name: 'Vouchers')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'type', type: 'string', enum: ['CONCRETE', 'PERCENTAGE']),
                new OA\Property(property: 'discount', type: 'float'),
                new OA\Property(property: 'expirationDate', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Updates a voucher with the provided values',
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
    #[Route('/{uuid}', name: 'update', methods: [Request::METHOD_PATCH])]
    public function update(
        Request $request,
        string $uuid,
        VoucherUpdateRequestValidator $requestValidator
    ): JsonResponse {
        $requestData = VoucherUpdateRequestData::createFromRequest($request)->setUuid($uuid);

        $requestValidator->validate($requestData);

        /** @var VoucherUpdateDto $return */
        $return = $this->voucherService->update($requestData);

        $responseData = SuccessfulResponseData::create()
            ->setMessage($return->getMessage())
            ->setData([VoucherEnum::UUID->value => $return->getVoucher()->getUuid()->toString()]);

        return $this->jsonResponse($responseData);
    }

    /**
     * Delete a voucher
     *
     * @throws \Throwable
     */
    #[OA\Tag(name: 'Vouchers')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Deletes a voucher',
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
    #[Route('/{uuid}', name: 'delete', methods: [Request::METHOD_DELETE])]
    public function delete(
        string $uuid,
        VoucherRemovalRequestValidator $requestValidator
    ): JsonResponse {
        $requestData = VoucherRemovalRequestData::create()->setUuid($uuid);

        $requestValidator->validate($requestData);

        /** @var VoucherRemovalDto $return */
        $return = $this->voucherService->delete($requestData);

        $responseData = SuccessfulResponseData::create()
            ->setMessage($return->getMessage())
            ->setData([VoucherEnum::UUID->value => $return->getUuid()->toString()]);

        return $this->jsonResponse($responseData);
    }
}
