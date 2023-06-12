<?php

declare(strict_types=1);

namespace App\Enum;

enum ResponseEnum: string
{
    case STATUS = 'status';
    case MESSAGE = 'message';
    case DATA = 'data';
    case ACTIONS = 'actions';
    case PAGINATION = 'pagination';
    case EXCEPTION = 'exception';
    case EXCEPTION_TRACE = 'exceptionTrace';
}
