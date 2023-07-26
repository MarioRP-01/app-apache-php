<?php

namespace Application\Traits;

use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\Stdlib\Exception\RuntimeException;

Trait RouterTrait
{

    /**
     * Generates an url given the name of a route.
     *
     * @see \Laminas\Router\RouteInterface::assemble()
     *
     * @param  string|null $name Name of the route
     * @param  array $params Parameters for the link
     * @param  array $options Options for the route
     * @return string Url For the link href attribute
     * @throws RuntimeException If no TreeRouteStack was provided.
     * @throws InvalidArgumentException If the params object has a name defined.
     */
    public function getRoute($name = null, $params = [], $options = [])
    {

        if (null === $this->router) {
            throw new RuntimeException('No TreeRouteStack instance provided');
        }

        /** @var TreeRouteStack $router */
        $router = $this->router;

        if (isset($params['name'])) {
            throw new InvalidArgumentException(
                'Cannot use "name" as a parameter; it is a reserved word'
            );
        }

        $options['name'] = $name;

        return (string) $router->assemble($params, $options);
    }
}