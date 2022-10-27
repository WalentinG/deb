<?php

declare(strict_types=1);

namespace support\bus;

final class Metadata
{
    public const HEADER_MESSAGE_TYPE = 'x-message-type';
    public const HEADER_CONTENT_TYPE = 'x-content-type';
    public const HEADER_TRACE_ID = 'x-trace-id';
    public const HEADER_MESSAGE_ID = 'x-message-id';

    /** @param array<string, int|float|string|null> $headers */
    public function __construct(public readonly array $headers = [])
    {
    }

    public function with(string $traceId, string $messageId, string $messageType, string $contentType): self
    {
        return new self([
            self::HEADER_MESSAGE_TYPE => $messageType,
            self::HEADER_CONTENT_TYPE => $contentType,
            self::HEADER_TRACE_ID => $traceId,
            self::HEADER_MESSAGE_ID => $messageId,
        ] + $this->headers);
    }

    public function messageId(): string
    {
        return toStr($this->headers[self::HEADER_MESSAGE_ID] ?? uuid());
    }

    public function traceId(): string
    {
        return toStr($this->headers[self::HEADER_TRACE_ID] ?? uuid());
    }

    public function messageType(): string
    {
        return toStr($this->headers[self::HEADER_MESSAGE_TYPE] ?? 'NA');
    }

    public function contentType(): string
    {
        return toStr($this->headers[self::HEADER_CONTENT_TYPE] ?? 'NA');
    }
}
