<?php

declare(strict_types=1);

namespace support\http;

abstract class IpAddress
{
    final public static function fromString(string $value): self
    {
        return substr_count($value, ':') > 1 ? new IpAddressV6(trim($value)) : new IpAddressV4(trim($value));
    }

    public static function withoutPort(string $value): self
    {
        return substr_count($value, ':') > 1 ? IpAddressV6::withoutPort(trim($value)) : IpAddressV4::withoutPort(trim($value));
    }

    /** Checks if an IP address is contained in the list of given IPs or subnets. */
    final public function containedIn(string ...$subnetOrIpList): bool
    {
        foreach ($subnetOrIpList as $address) {
            if ($this->containedInAddress($address)) {
                return true;
            }
        }

        return false;
    }

    abstract public function toString(): string;

    abstract protected function containedInAddress(string $address): bool;
}
