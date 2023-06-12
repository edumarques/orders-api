<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Contracts\Translation\TranslatorInterface;

abstract readonly class AbstractRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        protected TranslatorInterface $translator,
    ) {
    }
}
