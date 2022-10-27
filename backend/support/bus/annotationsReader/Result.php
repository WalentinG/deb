<?php

declare(strict_types=1);

namespace support\bus\annotationsReader;

use support\bus\annotationsReader\attribute\ClassLevel;
use support\bus\annotationsReader\attribute\MethodLevel;

final class Result
{
    /**
     * @param \SplObjectStorage<ClassLevel, int>  $classLevelCollection
     * @param \SplObjectStorage<MethodLevel, int> $methodLevelCollection
     */
    public function __construct(
        public readonly \SplObjectStorage $classLevelCollection,
        public readonly \SplObjectStorage $methodLevelCollection
    ) {
    }
}
