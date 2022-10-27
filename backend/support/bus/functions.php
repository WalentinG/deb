<?php

declare(strict_types=1);

namespace support\bus;

/**
 * @param string[] $paths
 *
 * @return string[]
 */
function canonicalizeFilesPath(array $paths): array
{
    $result = [];

    foreach ($paths as $path) {
        $result[] = (string)(new \SplFileInfo($path))->getRealPath();
    }

    return $result;
}

/** @param string[] $directories */
function searchFiles(array $directories, string $regExp): \Generator
{
    foreach ($directories as $directory) {
        yield from new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory)
            ),
            $regExp
        );
    }
}

function extractNamespaceFromFile(string $filePath, ?string $fileContents = null): ?string
{
    $fileContents ??= (string)file_get_contents($filePath);

    /* @phpstan-ignore-next-line */
    if (false !== preg_match('#^namespace\s+(.+?);$#sm', $fileContents, $matches) && isset($matches[1])) {
        /** @var string $fileName */
        $fileName = pathinfo($filePath)['filename'];

        return sprintf('%s\\%s', $matches[1], $fileName);
    }

    return null;
}

/** @return \Generator<class-string> */
function extractMessageClasses(\ReflectionMethod $reflectionMethod): \Generator
{
    $reflectionParameters = $reflectionMethod->getParameters();

    if (!$reflectionMethod->isPublic()) {
        throw InvalidHandlerMethod::wrongVisibility($reflectionMethod);
    }

    if (1 <= \count($reflectionParameters)) {
        $firstArgumentType = isset($reflectionParameters[0]) && null !== $reflectionParameters[0]->getType()
            ? $reflectionParameters[0]->getType()
            : null;

        if (null !== $firstArgumentType) {
            $reflectionType = $reflectionParameters[0]->getType();

            if ($reflectionType instanceof \ReflectionUnionType) {
                foreach ($reflectionType->getTypes() as $type) {
                    if ($type instanceof \ReflectionNamedType && class_exists($type->getName())) {
                        yield $type->getName();
                    }
                }

                return;
            }

            if ($reflectionType instanceof \ReflectionNamedType && class_exists($reflectionType->getName())) {
                yield $reflectionType->getName();

                return;
            }
        }

        throw InvalidHandlerMethod::wrongEventArgument($reflectionMethod);
    }

    throw InvalidHandlerMethod::tooManyArguments($reflectionMethod);
}
