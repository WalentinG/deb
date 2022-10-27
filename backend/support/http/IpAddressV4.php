<?php

declare(strict_types=1);

namespace support\http;

final class IpAddressV4 extends IpAddress
{
    public function __construct(private string $value)
    {
        if (!filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            throw new \InvalidArgumentException('Invalid value fot Ip address v4.');
        }
    }

    public function toString(): string
    {
        return $this->value;
    }

    public static function withoutPort(string $value): IpAddress
    {
        $i = strpos($value, ':');

        return new self($i ? substr($value, 0, $i) : $value);
    }

    protected function containedInAddress(string $address): bool
    {
        $netmask = 32;
        if (str_contains($address, '/')) {
            [$address, $netmask] = explode('/', $address, 2);

            if ('0' === $netmask) {
                return (bool)filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
            }

            if ($netmask < 0 || $netmask > 32) {
                return false;
            }

            $netmask = (int)$netmask;
        }

        if (false === ip2long($address)) {
            return false;
        }

        $left = sprintf('%032b', ip2long($this->value));
        $right = sprintf('%032b', ip2long($address));

        return 0 === substr_compare($left, $right, 0, $netmask);
    }
}
