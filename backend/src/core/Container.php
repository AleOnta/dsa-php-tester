<?php

namespace Backend\Core;

class Container
{
    private array $services = [];

    public function set(string $serviceKey, callable $resolver)
    {
        # store the service inside the container
        $this->services[$serviceKey] = $resolver;
    }

    public function get(string $serviceKey)
    {
        # check if the service is stored in the container
        if (!isset($this->services[$serviceKey])) {
            throw new \Exception("Service [{$serviceKey}] doesn't exists!");
        }
        # return the required service
        $service = $this->services[$serviceKey];
        return $service($this);
    }
}
