<?php

declare(strict_types=1);

namespace support\bus\endpoint;

final class EndpointRouter
{
    /**
     * Endpoints to which messages will be sent.
     *
     * name => [destination handler]
     *
     * @param array<class-string, Endpoint[]> $routes
     */
    public function __construct(private readonly Endpoint $defaultEndpoint, private readonly array $routes = [])
    {
    }

    public static function default(Endpoint $defaultEndpoint): self
    {
        return new self($defaultEndpoint);
    }

    /**
     * Add custom endpoint for multiple messages.
     *
     * @param class-string[] $messages
     */
    public function withRoutes(array $messages, Endpoint $endpoint): self
    {
        $routes = $this->routes;
        foreach ($messages as $messageClass) {
            if ($this->defaultEndpoint->name === $endpoint->name) {
                throw new \InvalidArgumentException(sprintf('Endpoint with name "%s" already registered', $endpoint->name));
            }

            foreach ($this->routes as $current) {
                foreach ($current as $route) {
                    if ($route->name === $endpoint->name) {
                        throw new \InvalidArgumentException(sprintf('Endpoint with name "%s" already registered', $endpoint->name));
                    }
                }
            }

            $routes[$messageClass][] = $endpoint;
        }

        return new self($this->defaultEndpoint, $routes);
    }

    /**
     * Receiving a message sending route
     * If no specific route is registered, the default endpoint route will be returned.
     *
     * @return Endpoint[]
     */
    public function route(string $messageClass): array
    {
        if (false === empty($this->routes[$messageClass])) {
            return $this->routes[$messageClass];
        }

        return [$this->defaultEndpoint];
    }
}
