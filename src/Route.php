<?php

namespace Lazy\Route;

class Route
{
    /**
     * The array of route methods.
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
     * The route handler.
     *
     * @var mixed
     */
    protected $handler;

    /**
     * The route name.
     *
     * @var string
     */
    protected $name;

    /**
     * The route namespace.
     *
     * @var string
     */
    protected $namespace;

    /**
     * The array of route parameter patterns.
     *
     * @var mixed[]
     */
    protected $patterns = [];

    /**
     * The array of route middleware.
     *
     * @var mixed[]
     */
    protected $middleware = [];

    /**
     * The route constructor.
     *
     * @param  mixed  $methods
     * @param  string  $pattern
     * @param  mixed  $handler
     */
    public function __construct($methods, string $pattern, $handler)
    {
        $methods = array_map('strtoupper', is_array($methods) ? $methods : explode('|', $methods));

        if (in_array('GET', $methods) && ! in_array('HEAD', $methods)) {
            $methods[] = 'HEAD';
        }

        $this->methods = $methods;
        $this->pattern = $pattern;
        $this->handler = $handler;
    }

    /**
     * Get the array of route methods.
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
     * Get the route handler.
     *
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Get the route name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get the route namespace.
     *
     * @return string|null
     */
    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * Get the array of route parameter patterns.
     *
     * @return mixed[]
     */
    public function getPatterns(): array
    {
        return $this->patterns;
    }

    /**
     * Get the array of route middleware.
     *
     * @return mixed[]
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Add the route middleware.
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
     * Set the route name.
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
     * Set the route parameter pattern.
     *
     * @param  string  $parameter
     * @param  string  $pattern
     *
     * @return self
     */
    public function pattern(string $parameter, string $pattern): self
    {
        $this->patterns[$parameter] = $pattern;
        return $this;
    }

    /**
     * Set the route namespace.
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
