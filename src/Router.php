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

use Closure;
use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Dionchaika\Container\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    /**
     * The allowed request methods.
     */
    const METHODS = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /**
     * The route collection.
     *
     * @var \Dionchaika\Router\RouteCollection
     */
    protected $routes;

    /**
     * The container
     * instance used by the router.
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * The array
     * of router middleware.
     *
     * @var mixed[]
     */
    protected $middleware = [];

    /**
     * The array
     * of route group attributes.
     *
     * @var mixed[]
     */
    protected $routeGroup = [];

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
    public function __construct(
        ?RouteCollection $routes       = null,
        ?ContainerInterface $container = null
    ) {
        $this->routes = $routes ?? new RouteCollection;
        $this->container = $container ?? new Container;
    }

    /**
     * Get the container
     * instance used by the router.
     *
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Set the container
     * instance used by the router.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @return self
     */
    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;
        return $this;
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
     * @return self
     */
    public function setRequestBasePath(string $path): self
    {
        $this->requestBasePath = '/'.trim($path, '/');
        return $this;
    }

    /**
     * Add a new router middleware.
     *
     * @param \Psr\Http\Server\MiddlewareInterface|\Closure|string $middleware
     * @return self
     */
    public function use($middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
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
        return $this->addRoute(self::METHODS, $pattern, $handler);
    }

    /**
     * Add a new fallback route.
     *
     * @param \Psr\Http\Server\RequestHandlerInterface|\Closure|string $handler
     * @return \Dionchaika\Router\Route
     */
    public function fallback($handler): Route
    {
        return $this->any('.*', $handler);
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
     * Add a new route group.
     *
     * @param mixed[]  $attributes
     * @param \Closure $callback
     * @return void
     */
    public function group(array $attributes = [], Closure $callback): void
    {
        //
    }

    /**
     * Match a request and return a response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function matchRequest(ServerRequestInterface $request): ResponseInterface
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

                $handler = new RequestHandler(function ($request) use ($route) {
                    return $route
                        ->getHandler()
                        ->setContainer($this->container)
                        ->handle($request);
                }, $this->middleware);

                return $handler
                    ->setContainer($this->container)
                    ->handle($request);
            }
        }

        throw new NotFoundException;
    }

    /**
     * Get the URI for the route.
     *
     * @param string  $name
     * @param mixed[] $parameters
     * @return \Psr\Http\Message\UriInterface
     * @throws \InvalidArgumentException
     */
    public function getUriFor(string $name, array $parameters = []): UriInterface
    {
        $this->routes->updateNamedRoutes();

        if ($this->routes->has($name)) {
            return $this->routes->get($name)->getUri($parameters);
        }

        throw new InvalidArgumentException(
            'Route does not exists: '.$name.'!'
        );
    }
}
