<?php

namespace Adldap\Schemas;

class ActiveDirectory extends Schema
{
    /**
     * {@inheritdoc}
     */
    public function distinguishedName(): string
    {
        return 'distinguishedname';
    }

    /**
     * {@inheritdoc}
     */
    public function distinguishedNameSubKey(): ?int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function filterEnabled(): string
    {
        return '(!(UserAccountControl:1.2.840.113556.1.4.803:=2))';
    }

    /**
     * {@inheritdoc}
     */
    public function filterDisabled(): string
    {
        return '(UserAccountControl:1.2.840.113556.1.4.803:=2)';
    }

    /**
     * {@inheritdoc}
     */
    public function lockoutTime(): string
    {
        return 'lockouttime';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassGroup(): string
    {
        return 'group';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassOu(): string
    {
        return 'organizationalunit';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassPerson(): string
    {
        return 'person';
    }

    /**
     * {@inheritdoc}
     */
    public function objectGuid(): string
    {
        return 'objectguid';
    }

    /**
     * {@inheritdoc}
     */
    public function objectGuidRequiresConversion(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategory(): string
    {
        return 'objectcategory';
    }
}
