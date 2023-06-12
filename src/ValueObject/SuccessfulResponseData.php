<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Enum\ResponseStatusEnum;

class SuccessfulResponseData extends AbstractResponseData
{
    protected string $status = ResponseStatusEnum::STATUS_SUCCESS->value;
}
