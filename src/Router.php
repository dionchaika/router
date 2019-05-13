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

use Dionchaika\Http\Response;
use Dionchaika\Container\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    /**
     * The route collection.
     *
     * @var \Dionchaika\Router\RouteCollection
     */
    protected $routes;

    /**
     * The router container.
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * The request base path.
     *
     * @var string
     */
    protected $requestBasePath = '';

    /**
     * @param \Dionchaika\Router\RouteCollection|null $routes
     * @param \Psr\Container\ContainerInterface|null  $container
     */
    public function __construct(?RouteCollection $routes = null, ?ContainerInterface $container = null)
    {
        $this->routes = $routes ?? new RouteCollection;
        $this->container = $container ?? new Container;
    }

    /**
     * Get the request base path.
     *
     * @return string
     */
    public function getRequestBasePath(): string
    {
        return $this->requestBasePath;
    }

    /**
     * Set the request base path.
     *
     * @param string $path
     * @return void
     */
    public function setRequestBasePath(string $path): void
    {
        $this->requestBasePath = '/'.trim($path, '/');
    }

    /**
     * Add a new route.
     *
     * @param string|string[]                                          $methods
     * @param string                                                   $pattern
     * @param \Psr\Http\Server\RequestHandlerInterface|\Closure|string $handler
     * @return \Dionchaika\Router\Route
     */
    public function addRoute($methods, string $pattern, $handler): Route
    {
        return $this->routes->add(new Route($methods, $pattern, $handler));
    }

    /**
     * Add a new GET method route.
     *
     * @param string                                                   $pattern
     * @param \Psr\Http\Server\RequestHandlerInterface|\Closure|string $handler
     * @return \Dionchaika\Router\Route
     */
    public function get(string $pattern, $handler): Route
    {
        return $this->addRoute('GET|HEAD', $pattern, $handler);
    }

    /**
     * Add a new HEAD method route.
     *
     * @param string                                                   $pattern
     * @param \Psr\Http\Server\RequestHandlerInterface|\Closure|string $handler
     * @return \Dionchaika\Router\Route
     */
    public function head(string $pattern, $handler): Route
    {
        return $this->addRoute('HEAD', $pattern, $handler);
    }

    /**
     * Add a new POST method route.
     *
     * @param string                                                   $pattern
     * @param \Psr\Http\Server\RequestHandlerInterface|\Closure|string $handler
     * @return \Dionchaika\Router\Route
     */
    public function post(string $pattern, $handler): Route
    {
        return $this->addRoute('POST', $pattern, $handler);
    }

    /**
     * Add a new PUT method route.
     *
     * @param string                                                   $pattern
     * @param \Psr\Http\Server\RequestHandlerInterface|\Closure|string $handler
     * @return \Dionchaika\Router\Route
     */
    public function put(string $pattern, $handler): Route
    {
        return $this->addRoute('PUT', $pattern, $handler);
    }

    /**
     * Add a new PATCH method route.
     *
     * @param string                                                   $pattern
     * @param \Psr\Http\Server\RequestHandlerInterface|\Closure|string $handler
     * @return \Dionchaika\Router\Route
     */
    public function patch(string $pattern, $handler): Route
    {
        return $this->addRoute('PATCH', $pattern, $handler);
    }

    /**
     * Add a new DELETE method route.
     *
     * @param string                                                   $pattern
     * @param \Psr\Http\Server\RequestHandlerInterface|\Closure|string $handler
     * @return \Dionchaika\Router\Route
     */
    public function delete(string $pattern, $handler): Route
    {
        return $this->addRoute('DELETE', $pattern, $handler);
    }

    /**
     * Add a new OPTIONS method route.
     *
     * @param string                                                   $pattern
     * @param \Psr\Http\Server\RequestHandlerInterface|\Closure|string $handler
     * @return \Dionchaika\Router\Route
     */
    public function options(string $pattern, $handler): Route
    {
        return $this->addRoute('OPTIONS', $pattern, $handler);
    }

    /**
     * Add a new any method route.
     *
     * @param string                                                   $pattern
     * @param \Psr\Http\Server\RequestHandlerInterface|\Closure|string $handler
     * @return \Dionchaika\Router\Route
     */
    public function any(string $pattern, $handler): Route
    {
        return $this->addRoute('GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS', $pattern, $handler);
    }

    /**
     * Match a request and return a response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function match(ServerRequestInterface $request): ResponseInterface
    {
        if ('' !== $this->requestBasePath) {
            $request = $request->withUri(
                $request->getUri()->withPath(
                    str_replace($this->requestBasePath, '', $request->getUri()->getPath())
                )
            );
        }

        foreach ($this->routes->all() as $route) {
            if ($route->isMatchesRequest($request)) {
                $request = $request->withAttribute('route', $route);

                foreach ($route->getParameters()->all() as $parameter) {
                    $request = $request->withAttribute(
                        $parameter->getName(),
                        $parameter->getValue()
                    );
                }

                return $route->getHandler()->handle($request);
            }
        }

        return new Response(404);
    }
}
