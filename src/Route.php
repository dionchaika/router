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

class Route
{
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
     * @var \Psr\Http\Server\RequestHandlerInterface
     */
    protected $handler;

    /**
     * Is the route
     * successfuly matches a request.
     *
     * @var bool
     */
    protected $success = false;

    /**
     * The array
     * of route parameters.
     *
     * @var mixed[]
     */
    protected $parameters = [];

    /**
     * @param string[]|string                          $methods
     * @param string                                   $pattern
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     */
    public function __construct($methods, string $pattern, RequestHandlerInterface $handler)
    {
        $this->methods = is_array($methods)
            ? $methods
            : explode('|', $methods);

        $this->pattern = $pattern;
        $this->handler = $handler;

        if (
            in_array('GET', $this->methods) &&
            !in_array('HEAD', $this->methods)
        ) {
            $this->methods[] = 'HEAD';
        }
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
     * Get the route pattern.
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
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
     * Is the route
     * successfuly matches a request.
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Is the route has a parameter.
     *
     * @param string $name
     * @return bool
     */
    public function hasParameter(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Get the route parameter.
     *
     * @param string $name
     * @return mixed
     */
    public function getParameter(string $name)
    {
        return $this->hasParameter($name) ? $this->parameters[$name] : null;
    }

    /**
     * @return mixed[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Is the route matches a request.
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

            $this->parameters[$parameterName] = null;
            $pattern = str_replace($value, $parameterPattern, $pattern);
        }

        if (
            in_array($request->getMethod(), $this->methods) &&
            preg_match('~^'.$pattern.'$~', $request->getUri()->getPath(), $matches)
        ) {
            array_shift($matches);
            $this->parameters = array_combine(array_keys($this->parameters), array_values($matches));

            return $this->success = true;
        }

        return false;
    }
}
