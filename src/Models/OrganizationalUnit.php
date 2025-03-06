<?php

namespace Adldap\Models;

/**
 * Class OrganizationalUnit.
 *
 * Represents an LDAP organizational unit.
 */
class OrganizationalUnit extends Entry
{
    use Concerns\HasDescription;

    /**
     * Retrieves the organization units OU attribute.
     *
     * @return string|null
     */
    public function getOu(): ?string
    {
        return $this->getFirstAttribute($this->schema->organizationalUnitShort());
    }

    /**
     * {@inheritdoc}
     */
    protected function getCreatableDn(): string|Attributes\DistinguishedName
    {
        return $this->getDnBuilder()->addOU($this->getOu());
    }
}
