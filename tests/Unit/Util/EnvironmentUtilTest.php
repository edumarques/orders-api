<?php

declare(strict_types=1);

namespace App\Tests\Unit\Util;

use App\Util\EnvironmentUtil;
use PHPUnit\Framework\TestCase;

final class EnvironmentUtilTest extends TestCase
{
    /**
     * @dataProvider dataTestIsDevEnvironment
     */
    public function testIsDevEnvironment(bool $expected, string $environment): void
    {
        $environmentUtility = $this->createEnvironmentUtil($environment);

        self::assertSame($expected, $environmentUtility->isDevEnvironment());
    }

    public function dataTestIsDevEnvironment(): \Generator
    {
        yield [true, 'dev'];
        yield [false, 'test'];
        yield [false, 'qa'];
        yield [false, 'prod'];
    }

    /**
     * @dataProvider dataTestIsTestEnvironment
     */
    public function testIsTestEnvironment(bool $expected, string $environment): void
    {
        $environmentUtility = $this->createEnvironmentUtil($environment);

        self::assertSame($expected, $environmentUtility->isTestEnvironment());
    }

    public function dataTestIsTestEnvironment(): \Generator
    {
        yield [false, 'dev'];
        yield [true, 'test'];
        yield [false, 'qa'];
        yield [false, 'prod'];
    }

    /**
     * @dataProvider dataTestIsProdEnvironment
     */
    public function testIsProdEnvironment(bool $expected, string $environment): void
    {
        $environmentUtility = $this->createEnvironmentUtil($environment);

        self::assertSame($expected, $environmentUtility->isProdEnvironment());
    }

    public function dataTestIsProdEnvironment(): \Generator
    {
        yield [false, 'dev'];
        yield [false, 'test'];
        yield [false, 'qa'];
        yield [true, 'prod'];
    }

    private function createEnvironmentUtil(string $environment): EnvironmentUtil
    {
        return new EnvironmentUtil($environment);
    }
}
