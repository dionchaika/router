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

class Parameter implements ParameterInterface
{
    /**
     * The parameter name.
     *
     * @var string
     */
    protected $name;

    /**
     * The parameter value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Get the parameter name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the parameter value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the parameter value.
     *
     * @param mixed $value
     * @return void
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
}
