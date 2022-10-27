<?php

declare(strict_types=1);

namespace support\bus\annotationsReader;

use support\bus\annotationsReader\attribute\ClassLevel;
use support\bus\annotationsReader\attribute\MethodLevel;
use support\bus\annotationsReader\exception\ParseAttributesFailed;

final class AttributesReader
{
    /** @param class-string $class */
    public function extract(string $class): Result
    {
        try {
            $reflectionClass = new \ReflectionClass($class);

            return new Result(
                classLevelCollection: $this->classLevelAnnotations($reflectionClass),
                methodLevelCollection: $this->methodMethodAnnotations($reflectionClass)
            );
        } catch (\Throwable $throwable) {
            throw new ParseAttributesFailed($throwable->getMessage(), (int)$throwable->getCode(), $throwable);
        }
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     *
     * @return \SplObjectStorage<MethodLevel, int>
     */
    private function methodMethodAnnotations(\ReflectionClass $reflectionClass): \SplObjectStorage
    {
        /** @phpstan-var \SplObjectStorage<MethodLevel, int> $methodLevelCollection */
        $methodLevelCollection = new \SplObjectStorage();

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            foreach ($reflectionMethod->getAttributes() as $reflectionAttribute) {
                $methodLevelCollection->attach(
                    new MethodLevel(
                        attribute: $reflectionAttribute->newInstance(),
                        inClass: $reflectionClass->name,
                        reflectionMethod: $reflectionMethod
                    )
                );
            }
        }

        return $methodLevelCollection;
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     *
     * @return \SplObjectStorage<ClassLevel, int>
     */
    private function classLevelAnnotations(\ReflectionClass $reflectionClass): \SplObjectStorage
    {
        /** @phpstan-var \SplObjectStorage<ClassLevel, int> $classLevelCollection */
        $classLevelCollection = new \SplObjectStorage();

        foreach ($reflectionClass->getAttributes() as $reflectionAttribute) {
            $classLevelCollection->attach(
                new ClassLevel(
                    attribute: $reflectionAttribute->newInstance(),
                    inClass: $reflectionClass->name
                )
            );
        }

        return $classLevelCollection;
    }
}
