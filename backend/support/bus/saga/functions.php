<?php

declare(strict_types=1);

namespace support\bus\saga;

use function support\bus\canonicalizeFilesPath;
use function support\bus\extractNamespaceFromFile;
use function support\bus\searchFiles;

/**
 * @param array<string> $directories
 * @param array<string> $excludedFiles
 *
 * @return class-string<Saga>[]
 */
function searchSagaHandlers(array $directories, array $excludedFiles = []): array
{
    $sagaPath = toStr((new \ReflectionClass(Saga::class))->getFileName());
    $excludedFiles = canonicalizeFilesPath(array_merge($excludedFiles, [$sagaPath]));

    $classes = [];
    /** @var \SplFileInfo $file */
    foreach (searchFiles($directories, '/\.php/i') as $file) {
        $filePath = (string)$file->getRealPath();

        if (true === \in_array($filePath, $excludedFiles, true)) {
            continue;
        }

        $fileContents = (string)file_get_contents($filePath);

        $class = extractNamespaceFromFile($filePath, $fileContents);

        if (null !== $class && is_a($class, Saga::class, true)) {
            $classes[] = extractNamespaceFromFile($filePath, $fileContents);
        }
    }
    /* @phpstan-ignore-next-line */
    return $classes;
}

/**
 * @template TValue
 *
 * @param TValue|\Traversable<TValue>|array<TValue> $argument
 *
 * @return array<TValue>
 */
function release(mixed $argument): array
{
    if (\is_array($argument)) {
        return $argument;
    }
    if ($argument instanceof \Traversable) {
        return iterator_to_array($argument, false);
    }
    if ($argument) {
        return [$argument];
    }

    return [];
}
