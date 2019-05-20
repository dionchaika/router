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

use Dionchaika\Http\Server\RequestHandler as Handler;

use Closure;
use RuntimeException;
use Dionchaika\Container\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestHandler extends Handler implements RequestHandlerInterface
{
    /**
     * The container instance
     * used by the request handler.
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * @param \Psr\Http\Server\RequestHandlerInterface|\Closure|string $fallbackHandler
     * @param mixed[]                                                  $middleware
     */
    public function __construct($fallbackHandler, array $middleware = []) {
        $this->container = new Container;
        parent::__construct($fallbackHandler, $middleware);
    }

    /**
     * Get the container
     * instance used by the request handler.
     *
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Set the container
     * instance used by the request handler.
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
     * Handle a request a return a response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \RuntimeException
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (0 === count($this->middleware)) {
            $fallbackHandler = $this->fallbackHandler;

            if ($fallbackHandler instanceof Closure) {
                return $fallbackHandler($request);
            }

            $fallbackHandler = !is_string($fallbackHandler)
                ? $fallbackHandler
                : (($this->container instanceof Container)
                    ? $this->container->make($fallbackHandler)
                    : $this->container->get($fallbackHandler));

            if ($fallbackHandler instanceof RequestHandlerInterface) {
                return $fallbackHandler->handle($request);
            }

            throw new RuntimeException(
                'Invalid fallback handler! '
                .'Fallback handler must be an instance of \\Closure '
                .'or an instance of \\Psr\\Http\\Server\\RequestHandlerInterface.'
            );
        }

        $middleware = array_shift($this->middleware);

        if ($middleware instanceof Closure) {
            return $middleware($request, $this);
        }

        $middleware = !is_string($middleware)
            ? $middleware
            : (($this->container instanceof Container)
                ? $this->container->make($middleware)
                : $this->container->get($middleware));

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }

        throw new RuntimeException(
            'Invalid middleware! '
            .'Middleware must be an instance of \\Closure '
            .'or an instance of \\Psr\\Http\\Server\\MiddlewareInterface.'
        );
    }

    /**
     * Handle a request a return a response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handle($request);
    }
}
