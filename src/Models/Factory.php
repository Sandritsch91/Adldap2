<?php

namespace Adldap\Models;

use Adldap\Query\Builder;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Schemas\SchemaInterface;

/**
 * Class Factory.
 *
 * Creates new LDAP models.
 */
class Factory
{
    /**
     * The LDAP query builder.
     *
     * @var Builder
     */
    protected Builder $query;

    /**
     * The LDAP schema.
     *
     * @var SchemaInterface
     */
    protected SchemaInterface $schema;

    /**
     * Constructor.
     *
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->setQuery($builder)
            ->setSchema($builder->getSchema());
    }

    /**
     * Sets the current query builder.
     *
     * @param Builder $builder
     *
     * @return $this
     */
    public function setQuery(Builder $builder): static
    {
        $this->query = $builder;

        return $this;
    }

    /**
     * Sets the current schema.
     *
     * If null is given, a default ActiveDirectory schema is set.
     *
     * @param SchemaInterface|null $schema
     *
     * @return $this
     */
    public function setSchema(?SchemaInterface $schema = null): static
    {
        $this->schema = $schema ?: new ActiveDirectory();

        return $this;
    }

    /**
     * Creates a new generic LDAP entry instance.
     *
     * @param array $attributes
     *
     * @return Entry
     */
    public function entry(array $attributes = []): Entry
    {
        $model = $this->schema->entryModel();

        return new $model($attributes, $this->query);
    }

    /**
     * Creates a new user instance.
     *
     * @param array $attributes
     *
     * @return User
     */
    public function user(array $attributes = []): User
    {
        $model = $this->schema->userModel();

        return (new $model($attributes, $this->query))
            ->setAttribute($this->schema->objectClass(), $this->schema->userObjectClasses());
    }

    /**
     * Creates a new organizational unit instance.
     *
     * @param array $attributes
     *
     * @return OrganizationalUnit
     */
    public function ou(array $attributes = []): OrganizationalUnit
    {
        $model = $this->schema->organizationalUnitModel();

        return (new $model($attributes, $this->query))
            ->setAttribute($this->schema->objectClass(), [
                $this->schema->top(),
                $this->schema->organizationalUnit(),
            ]);
    }

    /**
     * Creates a new organizational unit instance.
     *
     * @param array $attributes
     *
     * @return Organization
     */
    public function organization(array $attributes = []): Organization
    {
        $model = $this->schema->organizationModel();

        return (new $model($attributes, $this->query))
            ->setAttribute($this->schema->objectClass(), [
                $this->schema->top(),
                $this->schema->organization(),
            ]);
    }

    /**
     * Creates a new group instance.
     *
     * @param array $attributes
     *
     * @return Group
     */
    public function group(array $attributes = []): Group
    {
        $model = $this->schema->groupModel();

        return (new $model($attributes, $this->query))
            ->setAttribute($this->schema->objectClass(), [
                $this->schema->top(),
                $this->schema->objectCategoryGroup(),
            ]);
    }

    /**
     * Creates a new organizational unit instance.
     *
     * @param array $attributes
     *
     * @return Container
     */
    public function container(array $attributes = []): Container
    {
        $model = $this->schema->containerModel();

        return (new $model($attributes, $this->query))
            ->setAttribute($this->schema->objectClass(), $this->schema->objectClassContainer());
    }

    /**
     * Creates a new user instance as a contact.
     *
     * @param array $attributes
     *
     * @return Contact
     */
    public function contact(array $attributes = []): Contact
    {
        $model = $this->schema->contactModel();

        return (new $model($attributes, $this->query))
            ->setAttribute($this->schema->objectClass(), [
                $this->schema->top(),
                $this->schema->person(),
                $this->schema->organizationalPerson(),
                $this->schema->contact(),
            ]);
    }

    /**
     * Creates a new computer instance.
     *
     * @param array $attributes
     *
     * @return Computer
     */
    public function computer(array $attributes = []): Computer
    {
        $model = $this->schema->computerModel();

        return (new $model($attributes, $this->query))
            ->setAttribute($this->schema->objectClass(), [
                $this->schema->top(),
                $this->schema->person(),
                $this->schema->organizationalPerson(),
                $this->schema->user(),
                $this->schema->computer(),
            ]);
    }
}
