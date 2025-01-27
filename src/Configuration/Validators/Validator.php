<?php

namespace Adldap\Configuration\Validators;

use Adldap\Configuration\ConfigurationException;

/**
 * Class Validator.
 *
 * Validates configuration values.
 */
abstract class Validator
{
    /**
     * The configuration key under validation.
     *
     * @var string
     */
    protected string $key;

    /**
     * The configuration value under validation.
     *
     * @var mixed
     */
    protected mixed $value;

    /**
     * Constructor.
     *
     * @param string $key
     * @param mixed $value
     */
    public function __construct(string $key, mixed $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Validates the configuration value.
     *
     * @return true
     * @throws ConfigurationException When the value given fails validation.
     *
     */
    abstract public function validate(): true;
}
