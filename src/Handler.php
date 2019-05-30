<?php

namespace Lazy\Router;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Handler implements RequestHandlerInterface
{
    /**
     * The request handler container.
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * The array of request handler middleware.
     *
     * @var mixed[]
     */
    protected $middleware = [];

    /**
     * The fallback request handler.
     *
     * @var mixed
     */
    protected $fallbackHandler;

    /**
     * The request handler constructor.
     *
     * @param  \Psr\Container\ContainerInterface  $container
     * @param  mixed  $fallbackHandler
     */
    public function __construct(ContainerInterface $container, $fallbackHandler)
    {
        $this->container = $container;
        $this->fallbackHandler = $fallbackHandler;
    }

    /**
     * Get the request handler container.
     *
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Get the array of request handler middleware.
     *
     * @return mixed[]
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Get the fallback request handler.
     *
     * @return mixed
     */
    public function getFallbackHandler()
    {
        return $this->fallbackHandler;
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

            //

        }

        $middleware = array_shift($this->middleware);

        //

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
