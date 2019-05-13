<?php

/**
 * The PSR RESTful Router.
 *
 * @package dionchaika/router
 * @version 1.0.0
 * @license MIT
 * @author Dion Chaika <dionchaika@gmail.com>
 */

namespace Dionchaika\Router;

use InvalidArgumentException;

class RouteCollection
{
    /**
     * The array of routes.
     *
     * @var \Dionchaika\Router\Route[]
     */
    protected $routes = [];

    /**
     * The array of named routes.
     *
     * @var mixed[]
     */
    protected $namedRoutes = [];

    /**
     * @param \Dionchaika\Router\Route[] $routes
     * @throws \InvalidArgumentException
     */
    public function __construct(array $routes = [])
    {
        foreach ($routes as $route) {
            if (!($routes instanceof Route)) {
                throw new InvalidArgumentException(
                    'Route must be an instance of '
                    .'\\Dionchaika\\Router\\Route!'
                );
            }

            $this->add($route);
        }
    }

    /**
     * Add a new route
     * to the collection.
     *
     * @param \Dionchaika\Router\Route $route
     * @return \Dionchaika\Router\Route
     */
    public function add(Route $route): Route
    {
        $this->routes[] = $route;
        if ('' !== $route->getName()) {
            $this->namedRoutes[$route->getName()] = $route;
        }

        return $route;
    }

    /**
     * Get all of the routes in the collection.
     *
     * @return \Dionchaika\Router\Route[]
     */
    public function all(): array
    {
        return $this->routes;
    }

    /**
     * Check is the named route
     * exists in the collection.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->namedRoutes[$name]);
    }

    /**
     * Get named route.
     *
     * @param string $name
     * @return \Dionchaika\Router\Route|null
     */
    public function get(string $name): ?Route
    {
        return $this->has($name) ? $this->namedRoutes[$name] : null;
    }

    /**
     * Update the array of named routes.
     *
     * @return void
     */
    public function updateNamedRoutes(): void
    {
        foreach ($this->routes as $route) {
            if ('' !== $route->getName()) {
                $this->namedRoutes[$route->getName()] = $route;
            }
        }
    }
}
