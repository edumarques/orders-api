<?php

declare(strict_types=1);

namespace App\Util;

final readonly class EnvironmentUtil
{
    public function __construct(
        private string $environment
    ) {
    }

    /**
     * @codeCoverageIgnore
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function isDevEnvironment(): bool
    {
        return $this->environment === 'dev';
    }

    public function isTestEnvironment(): bool
    {
        return $this->environment === 'test';
    }

    public function isProdEnvironment(): bool
    {
        return $this->environment === 'prod';
    }
}
