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

use InvalidArgumentException;

class ParameterCollection
{
    /**
     * The array of parameters.
     *
     * @var mixed[]
     */
    protected $parameters = [];

    /**
     * The array of all of the parameters.
     *
     * @var \Dionchaika\Router\ParameterInterface[]
     */
    protected $allParameters = [];

    /**
     * @param \Dionchaika\Router\ParameterInterface[] $parameters
     * @throws \InvalidArgumentException
     */
    public function __construct(array $parameters = [])
    {
        foreach ($parameters as $parameter) {
            if (!($parameter instanceof ParameterInterface)) {
                throw new InvalidArgumentException(
                    'Parameter must be an instance of '
                    .'\\Dionchaika\\Router\\ParameterInterface!'
                );
            }

            $this->add($parameter);
        }
    }

    /**
     * Add a new parameter to the collection.
     *
     * @param \Dionchaika\Router\ParameterInterface $parameter
     * @return \Dionchaika\Router\ParameterInterface
     */
    public function add(ParameterInterface $parameter): ParameterInterface
    {
        $this->allParameters[] = $parameter;
        return $this->parameters[$parameter->getName()] = $parameter;
    }

    /**
     * Get all of the parameters in the collection.
     *
     * @return \Dionchaika\Router\ParameterInterface[]
     */
    public function all(): array
    {
        return $this->allParameters;
    }

    /**
     * Check is the parameter
     * exists in the collection.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Get parameter.
     *
     * @param string $name
     * @return \Dionchaika\Router\ParameterInterface|null
     */
    public function get(string $name): ?ParameterInterface
    {
        return $this->has($name) ? $this->parameters[$name] : null;
    }

    /**
     * Delete a parameter
     * from the collection.
     *
     * @param string $name
     * @return self
     */
    public function delete(string $name): self
    {
        unset($this->parameters[$name]);
        return $this;
    }
}
