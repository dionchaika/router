<?php

/**
 * The PSR RESTful Router Library.
 *
 * @package dionchaika/router
 * @version 1.0.0
 * @license MIT
 * @author Dion Chaika <dionchaika@gmail.com>
 */

namespace Dionchaika\Router;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    /**
     * The array of routes.
     *
     * @var \Dionchaika\Router\Route[]
     */
    protected $routes = [];

    /**
     * Add a new route.
     *
     * @param \Dionchaika\Router\Route|string[]|string      $methods
     * @param string|null                                   $pattern
     * @param \Psr\Http\Server\RequestHandlerInterface|null $handler
     * @return self
     */
    public function addRoute($methods, ?string $pattern = null, ?RequestHandlerInterface $handler = null): self
    {
        if ($methods instanceof Route) {
            $this->routes[] = $methods;
        } else {
            $this->routes[] = new Route($methods, $pattern, $handler);
        }

        return $this;
    }

    /**
     * Match routes to the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Dionchaika\Router\Route
     */
    public function match(ServerRequestInterface $request): Route
    {
        foreach ($this->routes as $route) {
            if ($route->isMatchesRequest($request)) {
                return $route;
            }
        }

        return new Route('GET|HEAD', '/.*', new NotFoundHandler);
    }
}
