<?php

namespace Lazy\Router;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    /**
     * The allowed methods.
     */
    const ALLOWED_METHODS = [

        'GET',
        'HEAD',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS'

    ];

    /**
     * The array of routes.
     *
     * @var mixed[]
     */
    protected $routes = [];

    /**
     * The array of all of the routes.
     *
     * @var mixed[]
     */
    protected $allRoutes = [];

    /**
     * The array of named routes.
     *
     * @var mixed[]
     */
    protected $namedRoutes = [];

    /**
     * The route group stack.
     *
     * @var \Lazy\Router\RouteGroup[]
     */
    protected $routeGroupStack = [];

    /**
     * Get the array of all of the routes.
     *
     * @return \Lazy\Router\Route[]
     */
    public function getRoutes(): array
    {
        return array_values($this->allRoutes);
    }

    /**
     * Register a GET method route.
     *
     * @param  string  $pattern
     * @param  mixed  $handler
     *
     * @return \Lazy\Router\Route
     */
    public function get(string $pattern, $handler): Route
    {
        return $this->route('GET|HEAD', $pattern, $handler);
    }

    /**
     * Register a HEAD method route.
     *
     * @param  string  $pattern
     * @param  mixed  $handler
     *
     * @return \Lazy\Router\Route
     */
    public function head(string $pattern, $handler): Route
    {
        return $this->route('HEAD', $pattern, $handler);
    }

    /**
     * Register a POST method route.
     *
     * @param  string  $pattern
     * @param  mixed  $handler
     *
     * @return \Lazy\Router\Route
     */
    public function post(string $pattern, $handler): Route
    {
        return $this->route('POST', $pattern, $handler);
    }

    /**
     * Register a PUT method route.
     *
     * @param  string  $pattern
     * @param  mixed  $handler
     *
     * @return \Lazy\Router\Route
     */
    public function put(string $pattern, $handler): Route
    {
        return $this->route('PUT', $pattern, $handler);
    }

    /**
     * Register a PATCH method route.
     *
     * @param  string  $pattern
     * @param  mixed  $handler
     *
     * @return \Lazy\Router\Route
     */
    public function patch(string $pattern, $handler): Route
    {
        return $this->route('PATCH', $pattern, $handler);
    }

    /**
     * Register a DELETE method route.
     *
     * @param  string  $pattern
     * @param  mixed  $handler
     *
     * @return \Lazy\Router\Route
     */
    public function delete(string $pattern, $handler): Route
    {
        return $this->route('DELETE', $pattern, $handler);
    }

    /**
     * Register an OPTIONS method route.
     *
     * @param  string  $pattern
     * @param  mixed  $handler
     *
     * @return \Lazy\Router\Route
     */
    public function options(string $pattern, $handler): Route
    {
        return $this->route('OPTIONS', $pattern, $handler);
    }

    /**
     * Register an any method route.
     *
     * @param  string  $pattern
     * @param  mixed  $handler
     *
     * @return \Lazy\Router\Route
     */
    public function any(string $pattern, $handler): Route
    {
        return $this->route(self::ALLOWED_METHODS, $pattern, $handler);
    }

    /**
     * Register a route.
     *
     * @param  mixed  $methods
     * @param  string  $pattern
     * @param  mixed  $handler
     *
     * @return \Lazy\Router\Route
     */
    public function route($methods, string $pattern, $handler): Route
    {
        $route = new Route($methods, $pattern, $handler);

        foreach ($route->getMethods() as $method) {
            $this->routes[$method][$pattern] = $route;
        }

        return $this->allRoutes[implode('|', $route->getMethods()).' '.$pattern] = $route;
    }

    /**
     * Handle a request and return a response.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();

        if (array_key_exists($method, $this->routes)) {
            foreach ($this->routes[$method] as $route) {
                if ($route->isMatchesRequest($request)) {
                    return ($route->getHandler())();
                }
            }
        }

        throw new NotFoundException;
    }
}
