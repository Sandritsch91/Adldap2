<?php

namespace Adldap\Models;

/**
 * Class Organization.
 *
 * Represents an LDAP organization.
 */
class Organization extends Entry
{
    use Concerns\HasDescription;

    /**
     * Retrieves the organization units OU attribute.
     *
     * @return string
     */
    public function getOrganization(): string
    {
        return $this->getFirstAttribute($this->schema->organizationName());
    }

    /**
     * {@inheritdoc}
     */
    protected function getCreatableDn(): string|Attributes\DistinguishedName
    {
        return $this->getDnBuilder()->addO($this->getOrganization());
    }
}
