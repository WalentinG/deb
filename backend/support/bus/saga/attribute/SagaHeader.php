<?php

declare(strict_types=1);

namespace support\bus\saga\attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class SagaHeader
{
    /**
     * @param non-empty-array<string> $id
     * @param array<array<string>>    $uniq
     * @param non-empty-string        $table
     */
    public function __construct(
        public readonly string $table,
        public readonly array $id,
        public readonly array $uniq = []
    ) {
    }

    /**
     * @param non-empty-array<non-empty-string, float|int|bool|string> $id
     *
     * @return non-empty-string
     */
    public function sagaId(array $id): string
    {
        ksort($id);

        return $this->table . ':' . md5(toStr(json_encode($id)));
    }
}
