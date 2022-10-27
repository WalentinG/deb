<?php

declare(strict_types=1);

namespace support\http;

final class IpAddressV6 extends IpAddress
{
    public function __construct(private string $value)
    {
        if (!filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            throw new \InvalidArgumentException('Invalid value fot Ip address v6.');
        }
    }

    public static function withoutPort(string $value): IpAddress
    {
        $hasPort = str_starts_with($value, '[');

        return new self($hasPort ? substr($value, 1, strpos($value, ']', 1) - 1) : $value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    protected function containedInAddress(string $address): bool
    {
        if (!((\extension_loaded('sockets') && \defined('AF_INET6')) || @inet_pton('::1'))) {
            throw new \RuntimeException('Unable to check Ipv6. Check that PHP was not compiled with option "disable-ipv6".');
        }

        $netmask = 128;

        if (str_contains($address, '/')) {
            [$address, $netmask] = explode('/', $address, 2);

            if ('0' === $netmask) {
                return (bool)unpack('n*', (string)@inet_pton($address));
            }
            $netmask = (int)$netmask;
            if ($netmask < 1 || $netmask > 128) {
                return false;
            }
        }

        $bytesAddress = unpack('n*', (string)@inet_pton($address));
        $bytesTest = unpack('n*', (string)@inet_pton($this->value));

        if (!$bytesAddress || !$bytesTest) {
            return false;
        }

        for ($i = 1, $ceil = ceil($netmask / 16); $i <= $ceil; ++$i) {
            $left = $netmask - 16 * ($i - 1);
            $left = ($left <= 16) ? $left : 16;
            $mask = ~(0xFFFF >> $left) & 0xFFFF;
            if (($bytesAddress[$i] & $mask) !== ($bytesTest[$i] & $mask)) {
                return false;
            }
        }

        return true;
    }
}
