<?php

namespace Adldap\Configuration;

use Adldap\Connections\ConnectionInterface;
use Adldap\Schemas\ActiveDirectory;

/**
 * Class DomainConfiguration.
 *
 * Contains an array of configuration options for a single LDAP connection.
 */
class DomainConfiguration
{
    /**
     * The configuration options array.
     *
     * The default values for each key indicate the type of value it requires.
     *
     * @var array
     */
    protected array $options = [
        // An array of LDAP hosts.
        'hosts' => [],

        // The global LDAP operation timeout limit in seconds.
        'timeout' => 5,

        // The LDAP version to utilize.
        'version' => 3,

        // The port to use for connecting to your hosts.
        'port' => ConnectionInterface::PORT,

        // The schema to use for your LDAP connection.
        'schema' => ActiveDirectory::class,

        // The base distinguished name of your domain.
        'base_dn' => '',

        // The username to connect to your hosts with.
        'username' => '',

        // The password that is utilized with the above user.
        'password' => '',

        // The account prefix to use when authenticating users.
        'account_prefix' => null,

        // The account suffix to use when authenticating users.
        'account_suffix' => null,

        // Whether or not to use SSL when connecting to your hosts.
        'use_ssl' => false,

        // Whether or not to use TLS when connecting to your hosts.
        'use_tls' => false,

        // Whether or not follow referrals is enabled when performing LDAP operations.
        'follow_referrals' => false,

        // Custom LDAP options that you'd like to utilize.
        'custom_options' => [],
    ];

    /**
     * Constructor.
     *
     * @param array $options
     *
     * @throws ConfigurationException When an option value given is an invalid type.
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Sets a configuration option.
     *
     * Throws an exception if the specified option does
     * not exist, or if it's an invalid type.
     *
     * @param string $key
     * @param mixed $value
     *
     * @throws ConfigurationException When an option value given is an invalid type.
     */
    public function set(string $key, mixed $value): void
    {
        if ($this->validate($key, $value)) {
            $this->options[$key] = $value;
        }
    }

    /**
     * Returns the value for the specified configuration options.
     *
     * Throws an exception if the specified option does not exist.
     *
     * @param string $key
     *
     * @return mixed
     * @throws ConfigurationException When the option specified does not exist.
     *
     */
    public function get(string $key): mixed
    {
        if ($this->has($key)) {
            return $this->options[$key];
        }

        throw new ConfigurationException("Option $key does not exist.");
    }

    /**
     * Checks if a configuration option exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->options);
    }

    /**
     * Validates the new configuration option against its
     * default value to ensure it's the correct type.
     *
     * If an invalid type is given, an exception is thrown.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return true
     * @throws ConfigurationException When an option value given is an invalid type.
     *
     */
    protected function validate(string $key, mixed $value): true
    {
        $default = $this->get($key);

        if (is_array($default)) {
            $validator = new Validators\ArrayValidator($key, $value);
        } elseif (is_int($default)) {
            $validator = new Validators\IntegerValidator($key, $value);
        } elseif (is_bool($default)) {
            $validator = new Validators\BooleanValidator($key, $value);
        } elseif (is_string($default) && class_exists($default)) {
            $validator = new Validators\ClassValidator($key, $value);
        } else {
            $validator = new Validators\StringOrNullValidator($key, $value);
        }

        return $validator->validate();
    }
}
