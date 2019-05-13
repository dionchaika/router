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

interface ParameterInterface
{
    /**
     * Get the parameter name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the parameter value.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Set the parameter value.
     *
     * @param mixed $value
     * @return void
     */
    public function setValue($value): void;
}
