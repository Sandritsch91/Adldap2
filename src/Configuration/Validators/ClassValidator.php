<?php

namespace Adldap\Configuration\Validators;

use Adldap\Configuration\ConfigurationException;

class ClassValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function validate(): true
    {
        if (!is_string($this->value) || !class_exists($this->value)) {
            throw new ConfigurationException("Option $this->key must be a valid class.");
        }

        return true;
    }
}
