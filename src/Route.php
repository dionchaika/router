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

use Dionchaika\Http\Uri;
use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Route
{
    /**
     * The route name.
     *
     * @var string
     */
    protected $name;

    /**
     * The array
     * of route methods.
     *
     * @var string[]
     */
    protected $methods;

    /**
     * The route pattern.
     *
     * @var string
     */
    protected $pattern;

    /**
     * The route request handler.
     *
     * @var \Dionchaika\Router\RequestHandler
     */
    protected $handler;

    /**
     * The router instance used by the route.
     *
     * @var \Dionchaika\Router\Router
     */
    protected $router;

    /**
     * The route parameter collection.
     *
     * @var \Dionchaika\Router\ParameterCollection
     */
    protected $parameters;

    /**
     * The array of headers
     * that should be in the request.
     *
     * @var mixed[]
     */
    protected $withHeaders = [];

    /**
     * The array of headers
     * that should not be in the request.
     *
     * @var string[]
     */
    protected $withoutHeaders = [];

    /**
     * @param string|string[]                                          $methods
     * @param string                                                   $pattern
     * @param \Psr\Http\Server\RequestHandlerInterface|\Closure|string $handler
     */
    public function __construct($methods, string $pattern, $handler)
    {
        $methods = is_array($methods)
            ? $methods
            : explode('|', $methods);

        if (
            in_array('GET', $methods) &&
            !in_array('HEAD', $methods)
        ) {
            $methods[] = 'HEAD';
        }

        $this->name = '';
        $this->methods = $methods;
        $this->pattern = $pattern;
        $this->handler = new RequestHandler($handler);

        $this->router = new Router;
        $this->parameters = new ParameterCollection;
    }

    /**
     * Get the router instance used by the route.
     *
     * @return \Dionchaika\Router\Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Set the router instance used by the route.
     *
     * @param \Dionchaika\Router\Router $router
     * @return self
     */
    public function setRouter(Router $router): self
    {
        $this->router = $router;
        return $this;
    }

    /**
     * Get the route name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the route name.
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the route pattern.
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Get the array
     * of route methods.
     *
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Get the route request handler.
     *
     * @return \Psr\Http\Server\RequestHandlerInterface
     */
    public function getHandler(): RequestHandlerInterface
    {
        return $this->handler;
    }

    /**
     * Add a new middleware to the route.
     *
     * @param \Psr\Http\Server\MiddlewareInterface|\Closure|string $middleware
     * @return self
     */
    public function addMiddleware($middleware): self
    {
        $this->handler->add($middleware);
        return $this;
    }

    /**
     * Add a new middleware to the route.
     *
     * An alias method name to addMiddleware.
     *
     * @param \Psr\Http\Server\MiddlewareInterface|\Closure|string $middleware
     * @return self
     */
    public function use($middleware): self
    {
        return $this->addMiddleware($middleware);
    }

    /**
     * Get the route parameter collection.
     *
     * @return \Dionchaika\Router\ParameterCollection
     */
    public function getParameters(): ParameterCollection
    {
        return $this->parameters;
    }

    /**
     * Add a header
     * whitch should be in the request.
     *
     * @param string $name
     * @param string $pattern
     * @return self
     */
    public function withHeader(string $name, string $pattern = '.*'): self
    {
        $this->withHeaders[$name] = $pattern;
        return $this;
    }

    /**
     * Add a header
     * whitch should not be in the request.
     *
     * @param string $header
     * @return self
     */
    public function withoutHeader(string $header): self
    {
        $this->withoutHeaders[] = $header;
        return $this;
    }

    /**
     * Check is the route matches request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return bool
     */
    public function isMatchesRequest(ServerRequestInterface $request): bool
    {
        $pattern = $this->pattern;

        preg_match_all('/\[([^\[\]]*)\:([^\[\]]+)\]/', $pattern, $matches);
        foreach ($matches[0] as $key => $value) {
            $parameterName = ('' !== $matches[1][$key])
                ? $matches[1][$key]
                : $matches[2][$key];

            $parameterPattern = ('' !== $matches[1][$key])
                ? '('.$matches[2][$key].')'
                : '([^\/]+)';

            $this->parameters->add(new Parameter($parameterName, null));
            $pattern = str_replace($value, $parameterPattern, $pattern);
        }

        if (
            in_array($request->getMethod(), $this->methods) &&
            preg_match('~^'.$pattern.'$~', $request->getUri()->getPath(), $matches)
        ) {
            foreach ($this->withHeaders as $name => $value) {
                if (
                    !$request->hasHeader($name) ||
                    !preg_match('/^'.preg_quote($value).'$/', $request->getHeaderLine($name))
                ) {
                    return false;
                }
            }

            foreach ($this->withoutHeaders as $header) {
                if ($request->hasHeader($header)) {
                    return false;
                }
            }

            array_shift($matches);
            foreach ($this->parameters->all() as $parameter) {
                $parameter->setValue(array_shift($matches));
            }

            return true;
        }

        return false;
    }

    /**
     * Get the URI for the route.
     *
     * @param mixed[] $parameters
     * @return \Psr\Http\Message\UriInterface
     * @throws \InvalidArgumentException
     */
    public function getUri(array $parameters = []): UriInterface
    {
        $uri = $this->pattern;

        preg_match_all('/\[([^\[\]]*)\:([^\[\]]+)\]/', $uri, $matches);
        foreach ($matches[0] as $key => $value) {
            $parameterName = ('' !== $matches[1][$key])
                ? $matches[1][$key]
                : $matches[2][$key];

            $parameterPattern = ('' !== $matches[1][$key])
                ? '('.$matches[2][$key].')'
                : '([^\/]+)';

            if (
                isset($parameters[$parameterName]) &&
                preg_match('/^'.$parameterPattern.'$/', $parameters[$parameterName])
            ) {
                $uri = str_replace($value, $parameters[$parameterName], $uri);
            } else {
                throw new InvalidArgumentException(
                    'Parameter is not passed: '.$parameterName.'!'
                );
            }
        }

        return new Uri($uri);
    }
}
