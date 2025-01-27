<?php

namespace Adldap\Schemas;

class Directory389 extends Schema
{
    /**
     * {@inheritdoc}
     */
    public function accountName(): string
    {
        return 'uid';
    }

    /**
     * {@inheritdoc}
     */
    public function distinguishedName(): string
    {
        return 'dn';
    }

    /**
     * {@inheritdoc}
     */
    public function distinguishedNameSubKey(): ?int
    {
        throw new \BadMethodCallException('Not implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function filterEnabled(): string
    {
        return sprintf('(!(%s=*))', $this->lockoutTime());
    }

    /**
     * {@inheritdoc}
     */
    public function filterDisabled(): string
    {
        return sprintf('(%s=*)', $this->lockoutTime());
    }

    /**
     * {@inheritdoc}
     */
    public function lockoutTime(): string
    {
        return 'pwdAccountLockedTime';
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategory(): string
    {
        return 'objectclass';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassGroup(): string
    {
        return 'groupofnames';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassOu(): string
    {
        return 'organizationalUnit';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassPerson(): string
    {
        return 'inetorgperson';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassUser(): string
    {
        return 'inetorgperson';
    }

    /**
     * {@inheritdoc}
     */
    public function objectGuid(): string
    {
        return 'nsuniqueid';
    }

    /**
     * {@inheritdoc}
     */
    public function objectGuidRequiresConversion(): bool
    {
        return false;
    }
}
