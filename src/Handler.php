<?php

namespace Lazy\Router;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

class Handler implements RequestHandlerInterface
{
    /**
     * The request handler container.
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * The fallback request handler.
     *
     * @var mixed
     */
    protected $fallbackHandler;

    /**
     * The array of request handler middleware.
     *
     * @var mixed[]
     */
    protected $middleware = [];

    /**
     * The request handler constructor.
     *
     * @param  \Psr\Container\ContainerInterface  $container
     * @param  mixed  $fallbackHandler
     * @param  mixed  $middleware
     */
    public function __construct(ContainerInterface $container, $fallbackHandler, $middleware)
    {
        $this->container = $container;
        $this->fallbackHandler = $fallbackHandler;
        $this->middleware = $middleware;
    }

    /**
     * Add the request handler middleware.
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
     * Handle a request and return a response.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (empty($this->middleware)) {
            if ($this->fallbackHandler instanceof Closure) {
                return $this->fallbackHandler($request);
            }

            if (method_exists($this->fallbackHandler, 'handle')) {
                return $this->fallbackHandler->handle($request);
            }
        }

        $middleware = array_shift($this->middleware);

        if ($middleware instanceof Closure) {
            return $middleware($request, $this);
        }

        if (method_exists($middleware, 'process')) {
            return $middleware->process($request, $this);
        }
    }

    /**
     * Handle a request and return a response.
     *
     * An alias method name to handle.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handle($request);
    }
}
