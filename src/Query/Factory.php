<?php

namespace Adldap\Query;

use Adldap\Connections\ConnectionInterface;
use Adldap\Models\RootDse;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Schemas\SchemaInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Adldap2 Search Factory.
 *
 * Constructs new LDAP queries.
 *
 *
 * @mixin Builder
 */
class Factory
{
    /**
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;

    /**
     * Stores the current schema instance.
     *
     * @var SchemaInterface
     */
    protected SchemaInterface $schema;

    /**
     * The base DN to use for the search.
     *
     * @var string|null
     */
    protected ?string $base;

    /**
     * The query cache.
     *
     * @var ?Cache
     */
    protected ?Cache $cache = null;

    /**
     * Constructor.
     *
     * @param ConnectionInterface $connection The connection to use when constructing a new query.
     * @param SchemaInterface|null $schema The schema to use for the query and models located.
     * @param string $baseDn The base DN to use for all searches.
     */
    public function __construct(ConnectionInterface $connection, ?SchemaInterface $schema = null, string $baseDn = '')
    {
        $this->setConnection($connection)
            ->setSchema($schema)
            ->setBaseDn($baseDn);
    }

    /**
     * Sets the connection property.
     *
     * @param ConnectionInterface $connection
     *
     * @return $this
     */
    public function setConnection(ConnectionInterface $connection): static
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Sets the schema property.
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
     * Sets the base distinguished name to perform searches upon.
     *
     * @param string $base
     *
     * @return $this
     */
    public function setBaseDn(string $base = ''): static
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Sets the cache for storing query results.
     *
     * @param Cache $cache
     *
     * @return $this
     */
    public function setCache(Cache $cache): static
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Returns a new query builder instance.
     *
     * @return Builder
     */
    public function newQuery(): Builder
    {
        return $this->newBuilder()->in($this->base);
    }

    /**
     * Performs a global 'all' search query on the current
     * connection by performing a search for all entries
     * that contain a common name attribute.
     *
     * @return Collection|array
     * @throws InvalidArgumentException
     */
    public function get(): array|Collection
    {
        return $this->newQuery()->whereHas($this->schema->commonName())->get();
    }

    /**
     * Returns a query builder scoped to users.
     *
     * @return Builder
     */
    public function users(): Builder
    {
        $wheres = [
            [$this->schema->objectClass(), Operator::$equals, $this->schema->objectClassUser()],
            [$this->schema->objectCategory(), Operator::$equals, $this->schema->objectCategoryPerson()],
        ];

        // OpenLDAP doesn't like specifying the omission of user objectclasses
        // equal to `contact`. We'll make sure we're working with
        // ActiveDirectory before adding this filter.
        if (is_a($this->schema, ActiveDirectory::class)) {
            $wheres[] = [$this->schema->objectClass(), Operator::$doesNotEqual, $this->schema->objectClassContact()];
        }

        return $this->where($wheres);
    }

    /**
     * Returns a query builder scoped to printers.
     *
     * @return Builder
     */
    public function printers(): Builder
    {
        return $this->where([
            $this->schema->objectClass() => $this->schema->objectClassPrinter(),
        ]);
    }

    /**
     * Returns a query builder scoped to organizational units.
     *
     * @return Builder
     */
    public function ous(): Builder
    {
        return $this->where([
            $this->schema->objectClass() => $this->schema->objectClassOu(),
        ]);
    }

    /**
     * Returns a query builder scoped to organizations.
     *
     * @return Builder
     */
    public function organizations(): Builder
    {
        return $this->where([
            $this->schema->objectClass() => $this->schema->objectClassOrganization(),
        ]);
    }

    /**
     * Returns a query builder scoped to groups.
     *
     * @return Builder
     */
    public function groups(): Builder
    {
        return $this->where([
            $this->schema->objectClass() => $this->schema->objectClassGroup(),
        ]);
    }

    /**
     * Returns a query builder scoped to containers.
     *
     * @return Builder
     */
    public function containers(): Builder
    {
        return $this->where([
            $this->schema->objectClass() => $this->schema->objectClassContainer(),
        ]);
    }

    /**
     * Returns a query builder scoped to contacts.
     *
     * @return Builder
     */
    public function contacts(): Builder
    {
        return $this->where([
            $this->schema->objectClass() => $this->schema->objectClassContact(),
        ]);
    }

    /**
     * Returns a query builder scoped to computers.
     *
     * @return Builder
     */
    public function computers(): Builder
    {
        return $this->where([
            $this->schema->objectClass() => $this->schema->objectClassComputer(),
        ]);
    }

    /**
     * Returns the root DSE record.
     *
     * @return RootDse|null
     * @throws InvalidArgumentException
     */
    public function getRootDse(): ?RootDse
    {
        $query = $this->newQuery();

        $root = $query->in('')->read()->whereHas($this->schema->objectClass())->first();

        if ($root) {
            return (new RootDse([], $query))
                ->setRawAttributes($root->getAttributes());
        }
        return null;
    }

    /**
     * Handle dynamic method calls on the query builder object.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return call_user_func_array([$this->newQuery(), $method], $parameters);
    }

    /**
     * Returns a new query grammar instance.
     *
     * @return Grammar
     */
    protected function newGrammar(): Grammar
    {
        return new Grammar();
    }

    /**
     * Returns a new query builder instance.
     *
     * @return Builder
     */
    protected function newBuilder(): Builder
    {
        $builder = new Builder($this->connection, $this->newGrammar(), $this->schema);

        $builder->setCache($this->cache);

        return $builder;
    }
}
