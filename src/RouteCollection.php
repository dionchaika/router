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
        return $this->routes[] = $route;
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
}
