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
     * The request handler container instance.
     *
     * @var \Dionchaika\Container\Container
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
     * Handle a request a return a response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \RuntimeException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (0 === count($this->middleware)) {
            $fallbackHandler = $this->fallbackHandler;

            if ($fallbackHandler instanceof Closure) {
                //
            }

            throw new RuntimeException(
                'Invalid fallback handler! '
                .'Fallback handler must be an instance of \\Closure '
                .'or an instance of \\Psr\\Http\\Server\\MiddlewareInterface'
            );
        }

        $middleware = array_shift($this->middleware);

        if ($middleware instanceof Closure) {
            return $middleware($request, $this);
        } else if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        } else if (is_string($middleware) && class_exists($middleware)) {
            $middleware = new $middleware;
            if (method_exists($middleware, ['process'])) {
                return $middleware->process($request, $this);
            }
        }

        throw new RuntimeException(
            'Invalid middleware! '
            .'Middleware must be an instance of \\Closure '
            .'or an instance of \\Psr\\Http\\Server\\MiddlewareInterface'
        );
    }
}
