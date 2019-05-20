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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestHandler extends Handler implements RequestHandlerInterface
{
    /**
     * The router instance
     * used by the request handler.
     *
     * @var \Dionchaika\Router\Router
     */
    protected $router;

    /**
     * @param \Psr\Http\Server\RequestHandlerInterface|\Closure|string $fallbackHandler
     * @param mixed[]                                                  $middleware
     */
    public function __construct($fallbackHandler, array $middleware = []) {
        $this->container = new Container;
        parent::__construct($fallbackHandler, $middleware);
    }

    /**
     * Get the router instance
     * used by the request handler.
     *
     * @return \Dionchaika\Router\Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Set the router instance
     * used by the request handler.
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
     * Handle a request a return a response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \RuntimeException
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $container = $this->router->getContainer();

        if (0 === count($this->middleware)) {
            $fallbackHandler = $this->fallbackHandler;

            if ($fallbackHandler instanceof Closure) {
                return $fallbackHandler($request);
            }

            $fallbackHandler = !is_string($fallbackHandler)
                ? $fallbackHandler
                : $this->container->make($fallbackHandler);

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
            : $this->container->make($middleware);

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
     * Invoke the request handler.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handle($request);
    }
}
