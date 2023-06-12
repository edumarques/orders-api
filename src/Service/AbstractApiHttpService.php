<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\Translation\TranslatorInterface;

abstract readonly class AbstractApiHttpService implements RequestValidationAwareInterface
{
    use RequestValidationAwareTrait;

    public function __construct(
        protected TranslatorInterface $translator
    ) {
    }
}
