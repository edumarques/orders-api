<?php

declare(strict_types=1);

namespace App\Tests;

trait TestHelperTrait
{
    /**
     * @throws \ReflectionException
     */
    private function setNonPublicPropertyValue(object $object, string $property, mixed $value): void
    {
        $property = new \ReflectionProperty($object, $property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);
    }
}
