<?php

namespace Lazy\Router;

use Closure;

class RouteGroup
{
    /**
     * The route group closure.
     *
     * @var \Closure
     */
    protected $closure;

    /**
     * The route group name.
     *
     * @var string
     */
    protected $name;

    /**
     * The route group namespace.
     *
     * @var string
     */
    protected $namespace;

    /**
     * The array of route group parameter patterns.
     *
     * @var mixed[]
     */
    protected $patterns = [];

    /**
     * The array of route group middleware.
     *
     * @var mixed[]
     */
    protected $middleware = [];

    /**
     * The route group constructor.
     *
     * @param  \Closure  $closure
     */
    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * Get the route group closure.
     *
     * @return \Closure
     */
    public function getClosure(): Closure
    {
        return $this->closure;
    }

    /**
     * Get the route group name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get the route group namespace.
     *
     * @return string|null
     */
    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * Get the array of route group parameter patterns.
     *
     * @return mixed[]
     */
    public function getPatterns(): array
    {
        return $this->patterns;
    }

    /**
     * Get the array of route group middleware.
     *
     * @return mixed[]
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Add the route group middleware.
     *
     * @param  mixed  $middleware
     *
     * @return self
     */
    public function use($middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    /**
     * Set the route group name.
     *
     * @param  string  $name
     *
     * @return self
     */
    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set the route group parameter pattern.
     *
     * @param  string  $param
     * @param  string  $pattern
     *
     * @return self
     */
    public function pattern(string $param, string $pattern): self
    {
        $this->patterns[$param] = $pattern;
        return $this;
    }

    /**
     * Set the route group namespace.
     *
     * @param  string  $namespace
     *
     * @return self
     */
    public function namespace(string $namespace): self
    {
        $this->namespace = '\\'.ltrim(str_replace('/', '\\', $namespace), '\\');
        return $this;
    }
}
