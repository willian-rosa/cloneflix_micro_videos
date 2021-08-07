<?php


namespace Tests\Traits;

use ReflectionClass;

trait ReflectionClassTrait
{
    protected function executeMethodInaccessibleWithArgs($className, $methodNameInaccessible, $object, array $args)
    {
        $reflectionClass = new ReflectionClass($className);
        $reflectionMethod = $reflectionClass->getMethod($methodNameInaccessible);
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invokeArgs($object, $args);
    }

    protected function executeMethodInaccessible($className, $methodNameInaccessible, $object)
    {
        $reflectionClass = new ReflectionClass($className);
        $reflectionMethod = $reflectionClass->getMethod($methodNameInaccessible);
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invoke($object);
    }

    protected static function getMethodProtected($className, $name) {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    protected function getValuePropertyProtected($className, $object, string $propertyName) {
        $class = new ReflectionClass($className);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }



}
