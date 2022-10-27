<?php

declare(strict_types=1);

namespace support\bus\serviceHandler;

use function support\bus\canonicalizeFilesPath;
use function support\bus\extractNamespaceFromFile;
use function support\bus\searchFiles;

/**
 * @param array<string> $directories
 * @param array<string> $excludedFiles
 *
 * @return class-string[]
 */
function searchHandlers(array $directories, array $excludedFiles = []): array
{
    $excludedFiles = canonicalizeFilesPath($excludedFiles);

    $classes = [];
    /** @var \SplFileInfo $file */
    foreach (searchFiles($directories, '/\.php/i') as $file) {
        $filePath = (string)$file->getRealPath();

        if (true === \in_array($filePath, $excludedFiles, true)) {
            continue;
        }

        $fileContents = (string)file_get_contents($filePath);

        if (str_contains($fileContents, '#[CommandHandler]') || str_contains($fileContents, '#[EventHandler]')) {
            $classes[] = extractNamespaceFromFile($filePath, $fileContents);
        }
    }
    /* @phpstan-ignore-next-line */
    return $classes;
}
