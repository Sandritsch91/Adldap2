<?php

namespace Adldap\Schemas;

class FreeIPA extends Schema
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
    public function objectCategory(): string
    {
        return 'objectclass';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassGroup(): string
    {
        return 'ipausergroup';
    }

    /**
     * {@inheritdoc}
     */
    public function userPrincipalName(): string
    {
        return 'krbCanonicalName';
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
    public function passwordLastSet(): string
    {
        return 'krbLastPwdChange';
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
    public function objectClassUser(): string
    {
        return 'organizationalPerson';
    }

    /**
     * {@inheritdoc}
     */
    public function objectGuid(): string
    {
        return 'ipaUniqueID';
    }

    /**
     * {@inheritdoc}
     */
    public function objectGuidRequiresConversion(): bool
    {
        return false;
    }
}
