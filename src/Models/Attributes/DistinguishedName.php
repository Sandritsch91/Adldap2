<?php

namespace Adldap\Models\Attributes;

use Adldap\Utilities;

class DistinguishedName
{
    /**
     * The distinguished name components (in order of assembly).
     *
     * @var array
     */
    protected array $components = [
        'cn' => [],
        'uid' => [],
        'ou' => [],
        'dc' => [],
        'o' => [],
    ];

    /**
     * Constructor.
     *
     * @param string|DistinguishedName|null $baseDn
     */
    public function __construct(string|DistinguishedName|null $baseDn = null)
    {
        $this->setBase($baseDn);
    }

    /**
     * Returns the complete distinguished name.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * Returns the complete distinguished name by assembling the RDN components.
     *
     * @return string
     */
    public function get(): string
    {
        $components = [];

        // We'll go through each component type and assemble its RDN.
        foreach ($this->components as $component => $values) {
            array_map(function ($value) use ($component, &$components) {
                // Assemble the component and escape the value.
                $components[] = sprintf('%s=%s', $component, ldap_escape((string)$value, '', 2));
            }, $values);
        }

        return implode(',', $components);
    }

    /**
     * Adds a domain component.
     *
     * @param string $dc
     *
     * @return DistinguishedName
     */
    public function addDc(string $dc): static
    {
        $this->addComponent('dc', $dc);

        return $this;
    }

    /**
     * Removes a domain component.
     *
     * @param string $dc
     *
     * @return DistinguishedName
     */
    public function removeDc(string $dc): static
    {
        $this->removeComponent('dc', $dc);

        return $this;
    }

    /**
     * Adds an organization name.
     *
     * @param string|null $o
     *
     * @return $this
     */
    public function addO(?string $o): static
    {
        $o = $o ?: '';
        $this->addComponent('o', $o);

        return $this;
    }

    /**
     * Removes an organization name.
     *
     * @param string $o
     *
     * @return DistinguishedName
     */
    public function removeO(string $o): static
    {
        $this->removeComponent('o', $o);

        return $this;
    }

    /**
     * Add a user identifier.
     *
     * @param string $uid
     *
     * @return DistinguishedName
     */
    public function addUid(string $uid): static
    {
        $this->addComponent('uid', $uid);

        return $this;
    }

    /**
     * Removes a user identifier.
     *
     * @param string $uid
     *
     * @return DistinguishedName
     */
    public function removeUid(string $uid): static
    {
        $this->removeComponent('uid', $uid);

        return $this;
    }

    /**
     * Adds a common name.
     *
     * @param ?string $cn
     *
     * @return DistinguishedName
     */
    public function addCn(?string $cn): static
    {
        $cn = $cn ?: '';
        $this->addComponent('cn', $cn);

        return $this;
    }

    /**
     * Removes a common name.
     *
     * @param string $cn
     *
     * @return DistinguishedName
     */
    public function removeCn(string $cn): static
    {
        $this->removeComponent('cn', $cn);

        return $this;
    }

    /**
     * Adds an organizational unit.
     *
     * @param string $ou
     *
     * @return DistinguishedName
     */
    public function addOu(string $ou): static
    {
        $this->addComponent('ou', $ou);

        return $this;
    }

    /**
     * Removes an organizational unit.
     *
     * @param string $ou
     *
     * @return DistinguishedName
     */
    public function removeOu(string $ou): static
    {
        $this->removeComponent('ou', $ou);

        return $this;
    }

    /**
     * Sets the base RDN of the distinguished name.
     *
     * @param string|DistinguishedName|null $base
     *
     * @return DistinguishedName
     */
    public function setBase(string|DistinguishedName|null $base): static
    {
        // Typecast base to string in case we've been given
        // an instance of the distinguished name object.
        $base = (string)$base;

        // If the base DN isn't null we'll try to explode it.
        $base = Utilities::explodeDn($base, false) ?: [];

        // Remove the count key from the exploded distinguished name.
        unset($base['count']);

        foreach ($base as $rdn) {
            // We'll break the RDN into pieces
            $pieces = explode('=', $rdn) ?: [];

            // If there's exactly 2 pieces, then we can work with it.
            if (count($pieces) === 2) {
                $attribute = ucfirst(strtolower($pieces[0]));

                $method = 'add' . $attribute;

                if (method_exists($this, $method)) {
                    // We see what type of RDN it is and add each accordingly.
                    call_user_func_array([$this, $method], [$pieces[1]]);
                }
            }
        }

        return $this;
    }

    /**
     * Returns an array of all components in the distinguished name.
     *
     * If a component name is given ('cn', 'dc' for example) then
     * the values of that component will be returned.
     *
     * @param string|null $component The component to retrieve values of
     *
     * @return array
     */
    public function getComponents(?string $component = null): array
    {
        if (is_null($component)) {
            return $this->components;
        }

        $this->validateComponentExists($component);

        return $this->components[$component];
    }

    /**
     * Adds a component to the distinguished name.
     *
     * @param string $component
     * @param string $value
     *
     * @throws \UnexpectedValueException When the given name does not exist.
     */
    protected function addComponent(string $component, string $value): void
    {
        $this->validateComponentExists($component);

        // We need to make sure the value we're given isn't empty before adding it into our components.
        if (!empty($value)) {
            $this->components[$component][] = $value;
        }
    }

    /**
     * Removes the given value from the given component.
     *
     * @param string $component
     * @param string $value
     *
     * @return void
     * @throws \UnexpectedValueException When the given component does not exist.
     *
     */
    protected function removeComponent(string $component, string $value): void
    {
        $this->validateComponentExists($component);

        $this->components[$component] = array_diff($this->components[$component], [$value]);
    }

    /**
     * Validates that the given component exists in the available components.
     *
     * @param string $component The name of the component to validate.
     *
     * @return void
     * @throws \UnexpectedValueException When the given component does not exist.
     *
     */
    protected function validateComponentExists(string $component): void
    {
        if (!array_key_exists($component, $this->components)) {
            throw new \UnexpectedValueException("The RDN component '$component' does not exist.");
        }
    }
}
