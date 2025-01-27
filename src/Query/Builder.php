<?php

namespace Adldap\Query;

use Adldap\Adldap;
use Adldap\Connections\ConnectionInterface;
use Adldap\Models\Model;
use Adldap\Models\ModelNotFoundException;
use Adldap\Query\Events\QueryExecuted;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Schemas\SchemaInterface;
use Adldap\Utilities;
use Closure;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use LDAP\Result;

class Builder
{
    /**
     * The selected columns to retrieve on the query.
     *
     * @var array
     */
    public array $columns = ['*'];

    /**
     * The query filters.
     *
     * @var array
     */
    public array $filters = [
        'and' => [],
        'or' => [],
        'raw' => [],
    ];

    /**
     * The size limit of the query.
     *
     * @var int
     */
    public int $limit = 0;

    /**
     * Determines whether the current query is paginated.
     *
     * @var bool
     */
    public bool $paginated = false;

    /**
     * The field to sort search results by.
     *
     * @var string
     */
    protected string $sortByField = '';

    /**
     * The direction to sort the results by.
     *
     * @var string
     */
    protected string $sortByDirection = '';

    /**
     * The sort flags for sorting query results.
     *
     * @var int|null
     */
    protected ?int $sortByFlags = null;

    /**
     * The distinguished name to perform searches upon.
     *
     * @var string|null
     */
    protected ?string $dn = null;

    /**
     * The default query type.
     *
     * @var string
     */
    protected string $type = 'search';

    /**
     * Determines whether or not to return LDAP results in their raw array format.
     *
     * @var bool
     */
    protected bool $raw = false;

    /**
     * Determines whether the query is nested.
     *
     * @var bool
     */
    protected bool $nested = false;

    /**
     * Determines whether the query should be cached.
     *
     * @var bool
     */
    protected bool $caching = false;

    /**
     * How long the query should be cached until.
     *
     * @var \DateTimeInterface|null
     */
    protected ?\DateTimeInterface $cacheUntil = null;

    /**
     * Determines whether the query cache must be flushed.
     *
     * @var bool
     */
    protected bool $flushCache = false;

    /**
     * The current connection instance.
     *
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;

    /**
     * The current grammar instance.
     *
     * @var Grammar
     */
    protected Grammar $grammar;

    /**
     * The current schema instance.
     *
     * @var SchemaInterface
     */
    protected SchemaInterface $schema;

    /**
     * The current cache instance.
     *
     * @var Cache|null
     */
    protected ?Cache $cache;

    /**
     * Constructor.
     *
     * @param ConnectionInterface $connection
     * @param Grammar|null $grammar
     * @param SchemaInterface|null $schema
     */
    public function __construct(
        ConnectionInterface $connection,
        ?Grammar $grammar = null,
        ?SchemaInterface $schema = null
    ) {
        $this->setConnection($connection)
            ->setGrammar($grammar)
            ->setSchema($schema);
    }

    /**
     * Sets the current connection.
     *
     * @param ConnectionInterface $connection
     *
     * @return Builder
     */
    public function setConnection(ConnectionInterface $connection): static
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Sets the current filter grammar.
     *
     * @param Grammar|null $grammar
     *
     * @return Builder
     */
    public function setGrammar(?Grammar $grammar = null): static
    {
        $this->grammar = $grammar ?: new Grammar();

        return $this;
    }

    /**
     * Sets the current schema.
     *
     * @param SchemaInterface|null $schema
     *
     * @return Builder
     */
    public function setSchema(?SchemaInterface $schema = null): static
    {
        $this->schema = $schema ?: new ActiveDirectory();

        return $this;
    }

    /**
     * Returns the current schema.
     *
     * @return SchemaInterface
     */
    public function getSchema(): SchemaInterface
    {
        return $this->schema;
    }

    /**
     * Sets the cache to store query results.
     *
     * @param Cache|null $cache
     * @return Builder
     */
    public function setCache(?Cache $cache = null): static
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Returns a new Query Builder instance.
     *
     * @param string|null $baseDn
     *
     * @return Builder
     */
    public function newInstance(?string $baseDn = null): Builder
    {
        // We'll set the base DN of the new Builder so
        // developers don't need to do this manually.
        $dn = is_null($baseDn) ? $this->getDn() : $baseDn;

        return (new static($this->connection, $this->grammar, $this->schema))
            ->setDn($dn);
    }

    /**
     * Returns a new nested Query Builder instance.
     *
     * @param Closure|null $closure
     *
     * @return $this
     */
    public function newNestedInstance(?Closure $closure = null): static
    {
        $query = $this->newInstance()->nested();

        if ($closure) {
            call_user_func($closure, $query);
        }

        return $query;
    }

    /**
     * Returns the current query.
     *
     * @return Collection|array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get(): array|Collection
    {
        // We'll mute any warnings / errors here. We just need to
        // know if any query results were returned.
        return @$this->query($this->getQuery());
    }

    /**
     * Compiles and returns the current query string.
     *
     * @return string
     */
    public function getQuery(): string
    {
        // We need to ensure we have at least one filter, as
        // no query results will be returned otherwise.
        if (count(array_filter($this->filters)) === 0) {
            $this->whereHas($this->schema->objectClass());
        }

        return $this->grammar->compile($this);
    }

    /**
     * Returns the unescaped query.
     *
     * @return string
     */
    public function getUnescapedQuery(): string
    {
        return Utilities::unescape($this->getQuery());
    }

    /**
     * Returns the current Grammar instance.
     *
     * @return Grammar
     */
    public function getGrammar(): Grammar
    {
        return $this->grammar;
    }

    /**
     * Returns the current Connection instance.
     *
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    /**
     * Returns the builders DN to perform searches upon.
     *
     * @return string
     */
    public function getDn(): string
    {
        return $this->dn ?? '';
    }

    /**
     * Sets the DN to perform searches upon.
     *
     * @param string|Model|null $dn
     *
     * @return Builder
     */
    public function setDn(Model|string|null $dn = null): static
    {
        $this->dn = $dn instanceof Model ? $dn->getDn() : $dn;

        return $this;
    }

    /**
     * Alias for setting the base DN of the query.
     *
     * @param string|Model|null $dn
     *
     * @return Builder
     */
    public function in(Model|string|null $dn = null): static
    {
        return $this->setDn($dn);
    }

    /**
     * Sets the size limit of the current query.
     *
     * @param int $limit
     *
     * @return Builder
     */
    public function limit(int $limit = 0): static
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Performs the specified query on the current LDAP connection.
     *
     * @param string $query
     *
     * @return Collection|array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function query(string $query): array|Collection
    {
        $start = microtime(true);

        // Here we will create the execution callback. This allows us
        // to only execute an LDAP request if caching is disabled
        // or if no cache of the given query exists yet.
        $callback = function () use ($query) {
            return $this->parse($this->run($query));
        };

        // If caching is enabled and we have a cache instance available,
        // we will try to retrieve the cached results instead.
        // Otherwise, we will simply execute the callback.
        if ($this->caching && $this->cache) {
            $results = $this->getCachedResponse($this->getCacheKey($query), $callback);
        } else {
            $results = $callback();
        }

        // Log the query.
        $this->logQuery($this, $this->type, $this->getElapsedTime($start));

        // Process & return the results.
        return $this->newProcessor()->process($results);
    }

    /**
     * Paginates the current LDAP query.
     *
     * @param int $perPage
     * @param int $currentPage
     * @param bool $isCritical
     *
     * @return Paginator
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function paginate(int $perPage = 1000, int $currentPage = 0, bool $isCritical = true): Paginator
    {
        $this->paginated = true;

        $start = microtime(true);

        $query = $this->getQuery();

        // Here we will create the pagination callback. This allows us
        // to only execute an LDAP request if caching is disabled
        // or if no cache of the given query exists yet.
        $callback = function () use ($query, $perPage, $isCritical) {
            return $this->runPaginate($query, $perPage, $isCritical);
        };

        // If caching is enabled and we have a cache instance available,
        // we will try to retrieve the cached results instead.
        if ($this->caching && $this->cache) {
            $pages = $this->getCachedResponse($this->getCacheKey($query), $callback);
        } else {
            $pages = $callback();
        }

        // Log the query.
        $this->logQuery($this, 'paginate', $this->getElapsedTime($start));

        // Process & return the results.
        return $this->newProcessor()->processPaginated($pages, $perPage, $currentPage);
    }

    /**
     * Get the cached response or execute and cache the callback value.
     *
     * @param string $key
     * @param Closure $callback
     *
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function getCachedResponse(string $key, Closure $callback): mixed
    {
        if ($this->flushCache) {
            $this->cache->delete($key);
        }

        return $this->cache->remember($key, $this->cacheUntil, $callback);
    }

    /**
     * Runs the query operation with the given filter.
     *
     * @param string $filter
     *
     * @return Result
     */
    // todo - add return type Result
    protected function run(string $filter): mixed
    {
        return $this->connection->{$this->type}(
            $this->getDn(),
            $filter,
            $this->getSelects(),
            false,
            $this->limit
        );
    }

    /**
     * Runs the paginate operation with the given filter.
     *
     * @param string $filter
     * @param int $perPage
     * @param bool $isCritical
     *
     * @return array
     */
    protected function runPaginate(string $filter, int $perPage, bool $isCritical): array
    {
        return $this->connection->supportsServerControlsInMethods() ?
            $this->compatiblePaginationCallback($filter, $perPage, $isCritical) :
            $this->deprecatedPaginationCallback($filter, $perPage, $isCritical);
    }

    /**
     * Create a deprecated pagination callback compatible with PHP 7.2.
     *
     * @param string $filter
     * @param int $perPage
     * @param bool $isCritical
     *
     * @return array
     */
    protected function deprecatedPaginationCallback(string $filter, int $perPage, bool $isCritical): array
    {
        $pages = [];

        $cookie = '';

        do {
            $this->connection->controlPagedResult($perPage, $isCritical, $cookie);

            if (!$resource = $this->run($filter)) {
                break;
            }

            // If we have been given a valid resource, we will retrieve the next
            // pagination cookie to send for our next pagination request.
            $this->connection->controlPagedResultResponse($resource, $cookie);

            $pages[] = $this->parse($resource);
        } while (!empty($cookie));

        // Reset paged result on the current connection. We won't pass in the current $perPage
        // parameter since we want to reset the page size to the default '1000'. Sending '0'
        // eliminates any further opportunity for running queries in the same request,
        // even though that is supposed to be the correct usage.
        $this->connection->controlPagedResult();

        return $pages;
    }

    /**
     * Create a compatible pagination callback compatible with PHP 7.3 and greater.
     *
     * @param string $filter
     * @param int $perPage
     * @param bool $isCritical
     *
     * @return array
     */
    protected function compatiblePaginationCallback(string $filter, int $perPage, bool $isCritical): array
    {
        $pages = [];

        // Setup our paged results control.
        $controls = [
            LDAP_CONTROL_PAGEDRESULTS => [
                'oid' => LDAP_CONTROL_PAGEDRESULTS,
                'isCritical' => $isCritical,
                'value' => [
                    'size' => $perPage,
                    'cookie' => '',
                ],
            ],
        ];

        do {
            // Update the server controls.
            $this->connection->setOption(LDAP_OPT_SERVER_CONTROLS, $controls);

            if (!$resource = $this->run($filter)) {
                break;
            }

            $errorCode = $dn = $errorMessage = $refs = null;

            // Update the server controls with the servers response.
            $this->connection->parseResult($resource, $errorCode, $dn, $errorMessage, $refs, $controls);

            $pages[] = $this->parse($resource);

            // Reset paged result on the current connection. We won't pass in the current $perPage
            // parameter since we want to reset the page size to the default '1000'. Sending '0'
            // eliminates any further opportunity for running queries in the same request,
            // even though that is supposed to be the correct usage.
            $controls[LDAP_CONTROL_PAGEDRESULTS]['value']['size'] = $perPage;
        } while (!empty($controls[LDAP_CONTROL_PAGEDRESULTS]['value']['cookie']));

        // After running the query, we will clear the LDAP server controls. This
        // allows the controls to be automatically reset before each new query
        // that is conducted on the same connection during each request.
        $this->connection->setOption(LDAP_OPT_SERVER_CONTROLS, []);

        return $pages;
    }

    /**
     * Parses the given LDAP result by retrieving its entries.
     *
     * @param Result|resource $result
     *
     * @return array|Model
     */
    protected function parse(mixed $result): array|Model
    {
        if (is_resource($result) || $result instanceof Result) {
            $entries = $this->connection->getEntries($result);
            if (is_null($entries)) {
                $entries = [];
            }

            // Free up memory.
            $this->connection->freeResult($result);
        } else {
            $entries = [];
        }

        return $entries;
    }

    /**
     * Returns the cache key.
     *
     * @param string $query
     *
     * @return string
     */
    protected function getCacheKey(string $query): string
    {
        $key = $this->connection->getHost()
            . $this->type
            . $this->getDn()
            . $query
            . implode('', $this->getSelects())
            . $this->limit
            . $this->paginated;

        return md5($key);
    }

    /**
     * Returns the first entry in a search result.
     *
     * @param array|string $columns
     *
     * @return Model|array|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function first(array|string $columns = []): Model|array|null
    {
        $results = $this->select($columns)->limit(1)->get();

        // Since results may be returned inside an array if `raw()`
        // is specified, then we'll use our array helper
        // to retrieve the first result.
        return Arr::get($results, 0);
    }

    /**
     * Returns the first entry in a search result.
     *
     * If no entry is found, an exception is thrown.
     *
     * @param array|string $columns
     *
     * @return Model|array
     * @throws ModelNotFoundException|\Psr\SimpleCache\InvalidArgumentException
     */
    public function firstOrFail(array|string $columns = []): Model|array
    {
        $record = $this->first($columns);

        if (!$record) {
            throw (new ModelNotFoundException())
                ->setQuery($this->getUnescapedQuery(), $this->getDn());
        }

        return $record;
    }

    /**
     * Finds a record by the specified attribute and value.
     *
     * @param string $attribute
     * @param string $value
     * @param array|string $columns
     *
     * @return Model|array|false
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function findBy(string $attribute, string $value, array|string $columns = []): Model|false|array
    {
        try {
            return $this->findByOrFail($attribute, $value, $columns);
        } catch (ModelNotFoundException) {
            return false;
        }
    }

    /**
     * Finds a record by the specified attribute and value.
     *
     * If no record is found an exception is thrown.
     *
     * @param string $attribute
     * @param string $value
     * @param array|string $columns
     *
     * @return Model|array
     * @throws ModelNotFoundException|\Psr\SimpleCache\InvalidArgumentException
     */
    public function findByOrFail(string $attribute, string $value, array|string $columns = []): Model|array
    {
        return $this->whereEquals($attribute, $value)->firstOrFail($columns);
    }

    /**
     * Finds a record using ambiguous name resolution.
     *
     * @param array|string $value
     * @param array|string $columns
     *
     * @return false|Model|array|Collection|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function find(array|string $value, array|string $columns = []): false|Model|array|Collection|null
    {
        if (is_array($value)) {
            return $this->findMany($value, $columns);
        }

        // If we're not using ActiveDirectory, we can't use ANR. We'll make our own query.
        if (!is_a($this->schema, ActiveDirectory::class)) {
            return $this->prepareAnrEquivalentQuery($value)->first($columns);
        }

        return $this->findBy($this->schema->anr(), $value, $columns);
    }

    /**
     * Finds multiple records using ambiguous name resolution.
     *
     * @param array $values
     * @param array $columns
     *
     * @return Collection|array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function findMany(array $values = [], array $columns = []): array|Collection
    {
        $this->select($columns);

        if (!is_a($this->schema, ActiveDirectory::class)) {
            $query = $this;

            foreach ($values as $value) {
                $query->prepareAnrEquivalentQuery($value);
            }

            return $query->get();
        }

        return $this->findManyBy($this->schema->anr(), $values);
    }

    /**
     * Creates an ANR equivalent query for LDAP distributions that do not support ANR.
     *
     * @param string $value
     *
     * @return Builder
     */
    protected function prepareAnrEquivalentQuery(string $value): static
    {
        return $this->orFilter(function (self $query) use ($value) {
            $locateBy = [
                $this->schema->name(),
                $this->schema->email(),
                $this->schema->userId(),
                $this->schema->lastName(),
                $this->schema->firstName(),
                $this->schema->commonName(),
                $this->schema->displayName(),
            ];

            foreach ($locateBy as $attribute) {
                $query->whereEquals($attribute, $value);
            }
        });
    }

    /**
     * Finds many records by the specified attribute.
     *
     * @param string $attribute
     * @param array $values
     * @param array $columns
     *
     * @return Collection|array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function findManyBy(string $attribute, array $values = [], array $columns = []): array|Collection
    {
        $query = $this->select($columns);

        foreach ($values as $value) {
            $query->orWhere([$attribute => $value]);
        }

        return $query->get();
    }

    /**
     * Finds a record using ambiguous name resolution.
     *
     * If a record is not found, an exception is thrown.
     *
     * @param string $value
     * @param array|string $columns
     *
     * @return Model|array
     * @throws ModelNotFoundException|\Psr\SimpleCache\InvalidArgumentException
     */
    public function findOrFail(string $value, array|string $columns = []): Model|array
    {
        $entry = $this->find($value, $columns);

        // Make sure we check if the result is an entry or an array before
        // we throw an exception in case the user wants raw results.
        if (!$entry instanceof Model && !is_array($entry)) {
            throw (new ModelNotFoundException())
                ->setQuery($this->getUnescapedQuery(), $this->getDn());
        }

        return $entry;
    }

    /**
     * Finds a record by its distinguished name.
     *
     * @param string|null $dn
     * @param array|string $columns
     *
     * @return Model|bool|array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function findByDn(?string $dn, array|string $columns = []): Model|bool|array
    {
        if (is_null($dn)) {
            $dn = '';
        }
        try {
            return $this->findByDnOrFail($dn, $columns);
        } catch (ModelNotFoundException) {
            return false;
        }
    }

    /**
     * Finds a record by its distinguished name.
     *
     * Fails upon no records returned.
     *
     * @param string $dn
     * @param array|string $columns
     *
     * @return Model|array
     * @throws ModelNotFoundException|\Psr\SimpleCache\InvalidArgumentException
     */
    public function findByDnOrFail(string $dn, array|string $columns = []): Model|array
    {
        // Since we're setting our base DN to be able to retrieve a model
        // by its distinguished name, we need to set it back to
        // our configured base so it is not overwritten.
        $base = $this->getDn();

        $model = $this->setDn($dn)
            ->read()
            ->whereHas($this->schema->objectClass())
            ->firstOrFail($columns);

        // Reset the models query builder (in case a model is returned).
        // Otherwise, we must be requesting a raw result.
        if ($model instanceof Model) {
            $model->setQuery($this->in($base));
        }

        return $model;
    }

    /**
     * Finds a record by its string GUID.
     *
     * @param string $guid
     * @param array|string $columns
     *
     * @return Model|array|false
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function findByGuid(string $guid, array|string $columns = []): Model|false|array
    {
        try {
            return $this->findByGuidOrFail($guid, $columns);
        } catch (ModelNotFoundException) {
            return false;
        }
    }

    /**
     * Finds a record by its string GUID.
     *
     * Fails upon no records returned.
     *
     * @param string $guid
     * @param array|string $columns
     *
     * @return Model|array
     * @throws ModelNotFoundException|\Psr\SimpleCache\InvalidArgumentException
     */
    public function findByGuidOrFail(string $guid, array|string $columns = []): Model|array
    {
        if ($this->schema->objectGuidRequiresConversion()) {
            $guid = Utilities::stringGuidToHex($guid);
        }

        return $this->select($columns)->whereRaw([
            $this->schema->objectGuid() => $guid,
        ])->firstOrFail();
    }

    /**
     * Finds a record by its Object SID.
     *
     * @param string $sid
     * @param array|string $columns
     *
     * @return Model|array|false
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function findBySid(string $sid, array|string $columns = []): Model|false|array
    {
        try {
            return $this->findBySidOrFail($sid, $columns);
        } catch (ModelNotFoundException) {
            return false;
        }
    }

    /**
     * Finds a record by its Object SID.
     *
     * Fails upon no records returned.
     *
     * @param string $sid
     * @param array|string $columns
     *
     * @return Model|array
     * @throws ModelNotFoundException|\Psr\SimpleCache\InvalidArgumentException
     */
    public function findBySidOrFail(string $sid, array|string $columns = []): Model|array
    {
        return $this->findByOrFail($this->schema->objectSid(), $sid, $columns);
    }

    /**
     * Finds the Base DN of your domain controller.
     *
     * @return string|bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function findBaseDn(): bool|string
    {
        $result = $this->setDn()
            ->read()
            ->raw()
            ->whereHas($this->schema->objectClass())
            ->first();

        $key = $this->schema->defaultNamingContext();

        if (is_array($result) && array_key_exists($key, $result)) {
            if (array_key_exists(0, $result[$key])) {
                return $result[$key][0];
            }
        }

        return false;
    }

    /**
     * Adds the inserted fields to query on the current LDAP connection.
     *
     * @param array|string $columns
     *
     * @return Builder
     */
    public function select(array|string $columns = []): static
    {
        $columns = is_array($columns) ? $columns : func_get_args();

        if (!empty($columns)) {
            $this->columns = $columns;
        }

        return $this;
    }

    /**
     * Adds a raw filter to the current query.
     *
     * @param array|string $filters
     *
     * @return Builder
     */
    public function rawFilter(array|string $filters = []): static
    {
        $filters = is_array($filters) ? $filters : func_get_args();

        foreach ($filters as $filter) {
            $this->filters['raw'][] = $filter;
        }

        return $this;
    }

    /**
     * Adds a nested 'and' filter to the current query.
     *
     * @param Closure $closure
     *
     * @return Builder
     */
    public function andFilter(Closure $closure): static
    {
        $query = $this->newNestedInstance($closure);

        $filter = $this->grammar->compileAnd($query->getQuery());

        return $this->rawFilter($filter);
    }

    /**
     * Adds a nested 'or' filter to the current query.
     *
     * @param Closure $closure
     *
     * @return Builder
     */
    public function orFilter(Closure $closure): static
    {
        $query = $this->newNestedInstance($closure);

        $filter = $this->grammar->compileOr($query->getQuery());

        return $this->rawFilter($filter);
    }

    /**
     * Adds a nested 'not' filter to the current query.
     *
     * @param Closure $closure
     *
     * @return Builder
     */
    public function notFilter(Closure $closure): static
    {
        $query = $this->newNestedInstance($closure);

        $filter = $this->grammar->compileNot($query->getQuery());

        return $this->rawFilter($filter);
    }

    /**
     * Adds a where clause to the current query.
     *
     * @param array|string $field
     * @param string|null $operator
     * @param string|null $value
     * @param string $boolean
     * @param bool $raw
     *
     * @return Builder
     */
    public function where(
        array|string $field,
        ?string $operator = null,
        ?string $value = '',
        string $boolean = 'and',
        bool $raw = false
    ): static {
        if (is_null($value)) {
            $value = '';
        }

        if (is_array($field)) {
            // If the column is an array, we will assume it is an array of
            // key-value pairs and can add them each as a where clause.
            return $this->addArrayOfWheres($field, $boolean, $raw);
        }

        // We'll bypass the 'has' and 'notHas' operator since they
        // only require two arguments inside the where method.
        $bypass = [Operator::$has, Operator::$notHas];

        // Here we will make some assumptions about the operator. If only
        // 2 values are passed to the method, we will assume that
        // the operator is 'equals' and keep going.
        if (func_num_args() === 2 && in_array($operator, $bypass) === false) {
            [$value, $operator] = [$operator, '='];
        }

        if (!in_array($operator, Operator::all())) {
            throw new InvalidArgumentException("Invalid where operator: $operator");
        }

        // We'll escape the value if raw isn't requested.
        $value = $raw ? $value : $this->escape($value);

        $field = $this->escape($field, '', 3);

        $this->addFilter($boolean, compact('field', 'operator', 'value'));

        return $this;
    }

    /**
     * Adds a raw where clause to the current query.
     *
     * Values given to this method are not escaped.
     *
     * @param array|string $field
     * @param string|null $operator
     * @param string|null $value
     *
     * @return Builder
     */
    public function whereRaw(array|string $field, ?string $operator = null, ?string $value = null): static
    {
        return $this->where($field, $operator, $value, 'and', true);
    }

    /**
     * Adds a 'where equals' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function whereEquals(string $field, string $value): static
    {
        return $this->where($field, Operator::$equals, $value);
    }

    /**
     * Adds a 'where not equals' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function whereNotEquals(string $field, string $value): static
    {
        return $this->where($field, Operator::$doesNotEqual, $value);
    }

    /**
     * Adds a 'where approximately equals' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function whereApproximatelyEquals(string $field, string $value): static
    {
        return $this->where($field, Operator::$approximatelyEquals, $value);
    }

    /**
     * Adds a 'where has' clause to the current query.
     *
     * @param string $field
     *
     * @return Builder
     */
    public function whereHas(string $field): static
    {
        return $this->where($field, Operator::$has);
    }

    /**
     * Adds a 'where not has' clause to the current query.
     *
     * @param string $field
     *
     * @return Builder
     */
    public function whereNotHas(string $field): static
    {
        return $this->where($field, Operator::$notHas);
    }

    /**
     * Adds a 'where contains' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function whereContains(string $field, string $value): static
    {
        return $this->where($field, Operator::$contains, $value);
    }

    /**
     * Adds a 'where contains' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function whereNotContains(string $field, string $value): static
    {
        return $this->where($field, Operator::$notContains, $value);
    }

    /**
     * Query for entries that match any of the values provided for the given field.
     *
     * @param string $field
     * @param array $values
     *
     * @return Builder
     */
    public function whereIn(string $field, array $values): static
    {
        return $this->orFilter(function (self $query) use ($field, $values) {
            foreach ($values as $value) {
                $query->whereEquals($field, $value);
            }
        });
    }

    /**
     * Adds a 'between' clause to the current query.
     *
     * @param string $field
     * @param array $values
     *
     * @return Builder
     */
    public function whereBetween(string $field, array $values): static
    {
        return $this->where([
            [$field, '>=', $values[0]],
            [$field, '<=', $values[1]],
        ]);
    }

    /**
     * Adds a 'where starts with' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function whereStartsWith(string $field, string $value): static
    {
        return $this->where($field, Operator::$startsWith, $value);
    }

    /**
     * Adds a 'where *not* starts with' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function whereNotStartsWith(string $field, string $value): static
    {
        return $this->where($field, Operator::$notStartsWith, $value);
    }

    /**
     * Adds a 'where ends with' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function whereEndsWith(string $field, string $value): static
    {
        return $this->where($field, Operator::$endsWith, $value);
    }

    /**
     * Adds a 'where *not* ends with' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function whereNotEndsWith(string $field, string $value): static
    {
        return $this->where($field, Operator::$notEndsWith, $value);
    }

    /**
     * Adds a enabled filter to the current query.
     *
     * @return Builder
     */
    public function whereEnabled(): static
    {
        return $this->rawFilter($this->schema->filterEnabled());
    }

    /**
     * Adds a disabled filter to the current query.
     *
     * @return Builder
     */
    public function whereDisabled(): static
    {
        return $this->rawFilter($this->schema->filterDisabled());
    }

    /**
     * Adds a 'member of' filter to the current query.
     *
     * @param string $dn
     *
     * @return Builder
     */
    public function whereMemberOf(string $dn): static
    {
        return $this->whereEquals($this->schema->memberOfRecursive(), $dn);
    }

    /**
     * Adds an 'or where' clause to the current query.
     *
     * @param array|string $field
     * @param string|null $operator
     * @param string|null $value
     *
     * @return Builder
     */
    public function orWhere(array|string $field, ?string $operator = null, ?string $value = null): static
    {
        return $this->where($field, $operator, $value, 'or');
    }

    /**
     * Adds a raw or where clause to the current query.
     *
     * Values given to this method are not escaped.
     *
     * @param string $field
     * @param string|null $operator
     * @param string|null $value
     *
     * @return Builder
     */
    public function orWhereRaw(string $field, ?string $operator = null, ?string $value = null): static
    {
        return $this->where($field, $operator, $value, 'or', true);
    }

    /**
     * Adds an 'or where has' clause to the current query.
     *
     * @param string $field
     *
     * @return Builder
     */
    public function orWhereHas(string $field): static
    {
        return $this->orWhere($field, Operator::$has);
    }

    /**
     * Adds a 'where not has' clause to the current query.
     *
     * @param string $field
     *
     * @return Builder
     */
    public function orWhereNotHas(string $field): static
    {
        return $this->orWhere($field, Operator::$notHas);
    }

    /**
     * Adds an 'or where equals' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereEquals(string $field, string $value): static
    {
        return $this->orWhere($field, Operator::$equals, $value);
    }

    /**
     * Adds an 'or where not equals' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereNotEquals(string $field, string $value): static
    {
        return $this->orWhere($field, Operator::$doesNotEqual, $value);
    }

    /**
     * Adds a 'or where approximately equals' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereApproximatelyEquals(string $field, string $value): static
    {
        return $this->orWhere($field, Operator::$approximatelyEquals, $value);
    }

    /**
     * Adds an 'or where contains' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereContains(string $field, string $value): static
    {
        return $this->orWhere($field, Operator::$contains, $value);
    }

    /**
     * Adds an 'or where *not* contains' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereNotContains(string $field, string $value): static
    {
        return $this->orWhere($field, Operator::$notContains, $value);
    }

    /**
     * Adds an 'or where starts with' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereStartsWith(string $field, string $value): static
    {
        return $this->orWhere($field, Operator::$startsWith, $value);
    }

    /**
     * Adds an 'or where *not* starts with' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereNotStartsWith(string $field, string $value): static
    {
        return $this->orWhere($field, Operator::$notStartsWith, $value);
    }

    /**
     * Adds an 'or where ends with' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereEndsWith(string $field, string $value): static
    {
        return $this->orWhere($field, Operator::$endsWith, $value);
    }

    /**
     * Adds an 'or where *not* ends with' clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereNotEndsWith(string $field, string $value): static
    {
        return $this->orWhere($field, Operator::$notEndsWith, $value);
    }

    /**
     * Adds an 'or where member of' filter to the current query.
     *
     * @param string $dn
     *
     * @return Builder
     */
    public function orWhereMemberOf(string $dn): static
    {
        return $this->orWhereEquals($this->schema->memberOfRecursive(), $dn);
    }

    /**
     * Adds a filter onto the current query.
     *
     * @param string $type The type of filter to add.
     * @param array $bindings The bindings of the filter.
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addFilter(string $type, array $bindings): static
    {
        // Here we will ensure we have been given a proper filter type.
        if (!array_key_exists($type, $this->filters)) {
            throw new InvalidArgumentException("Invalid filter type: $type.");
        }

        // The required filter key bindings.
        $required = ['field', 'operator', 'value'];

        // Here we will ensure the proper key bindings are given.
        if (count(array_intersect_key(array_flip($required), $bindings)) !== count($required)) {
            // Retrieve the keys that are missing in the bindings array.
            $missing = implode(', ', array_diff($required, array_flip($bindings)));

            throw new InvalidArgumentException("Invalid filter bindings. Missing: $missing keys.");
        }

        $this->filters[$type][] = $bindings;

        return $this;
    }

    /**
     * Clear the query builders filters.
     *
     * @return $this
     */
    public function clearFilters(): static
    {
        foreach ($this->filters as $type => $filters) {
            $this->filters[$type] = [];
        }

        return $this;
    }

    /**
     * Returns true / false depending if the current object
     * contains selects.
     *
     * @return bool
     */
    public function hasSelects(): bool
    {
        return count($this->getSelects()) > 0;
    }

    /**
     * Returns the current selected fields to retrieve.
     *
     * @return array
     */
    public function getSelects(): array
    {
        $selects = $this->columns;

        // If the asterisk is not provided in the selected columns, we need to
        // ensure we always select the object class and category, as these
        // are used for constructing models. The asterisk indicates that
        // we want all attributes returned for LDAP records.
        if (!in_array('*', $selects)) {
            $selects[] = $this->schema->objectCategory();
            $selects[] = $this->schema->objectClass();
        }

        return $selects;
    }

    /**
     * Sorts the LDAP search results by the specified field and direction.
     *
     * @param string $field
     * @param string $direction
     * @param int|null $flags
     *
     * @return Builder
     */
    public function sortBy(string $field, string $direction = 'asc', ?int $flags = null): static
    {
        $this->sortByField = $field;

        // Normalize direction.
        $direction = strtolower($direction);

        if ($direction === 'asc' || $direction === 'desc') {
            $this->sortByDirection = $direction;
        }

        if (is_null($flags)) {
            $this->sortByFlags = SORT_NATURAL + SORT_FLAG_CASE;
        }

        return $this;
    }

    /**
     * Set the query to search on the base distinguished name.
     *
     * This will result in one record being returned.
     *
     * @return Builder
     */
    public function read(): static
    {
        $this->type = 'read';

        return $this;
    }

    /**
     * Set the query to search one level on the base distinguished name.
     *
     * @return Builder
     */
    public function listing(): static
    {
        $this->type = 'listing';

        return $this;
    }

    /**
     * Sets the query to search the entire directory on the base distinguished name.
     *
     * @return Builder
     */
    public function recursive(): static
    {
        $this->type = 'search';

        return $this;
    }

    /**
     * Whether to return the LDAP results in their raw format.
     *
     * @param bool $raw
     *
     * @return Builder
     */
    public function raw(bool $raw = true): static
    {
        $this->raw = $raw;

        return $this;
    }

    /**
     * Whether the current query is nested.
     *
     * @param bool $nested
     *
     * @return Builder
     */
    public function nested(bool $nested = true): static
    {
        $this->nested = $nested;

        return $this;
    }

    /**
     * Enables caching on the current query until the given date.
     *
     * If flushing is enabled, the query cache will be flushed and then re-cached.
     *
     * @param \DateTimeInterface|null $until When to expire the query cache.
     * @param bool $flush Whether to force-flush the query cache.
     *
     * @return $this
     */
    public function cache(?\DateTimeInterface $until = null, bool $flush = false): static
    {
        $this->caching = true;
        $this->cacheUntil = $until;
        $this->flushCache = $flush;

        return $this;
    }

    /**
     * Returns an escaped string for use in an LDAP filter.
     *
     * @param string $value
     * @param string $ignore
     * @param int $flags
     *
     * @return string
     */
    public function escape(string $value, string $ignore = '', int $flags = 0): string
    {
        return ldap_escape($value, $ignore, $flags);
    }

    /**
     * Returns the query builders sort by field.
     *
     * @return string
     */
    public function getSortByField(): string
    {
        return $this->sortByField;
    }

    /**
     * Returns the query builders sort by direction.
     *
     * @return string
     */
    public function getSortByDirection(): string
    {
        return $this->sortByDirection;
    }

    /**
     * Returns the query builders sort by flags.
     *
     * @return int|null
     */
    public function getSortByFlags(): ?int
    {
        return $this->sortByFlags;
    }

    /**
     * Returns true / false if the current query is nested.
     *
     * @return bool
     */
    public function isNested(): bool
    {
        return $this->nested === true;
    }

    /**
     * Returns bool that determines whether the current
     * query builder will return raw results.
     *
     * @return bool
     */
    public function isRaw(): bool
    {
        return $this->raw;
    }

    /**
     * Returns bool that determines whether the current
     * query builder will return paginated results.
     *
     * @return bool
     */
    public function isPaginated(): bool
    {
        return $this->paginated;
    }

    /**
     * Returns bool that determines whether the current
     * query builder will return sorted results.
     *
     * @return bool
     */
    public function isSorted(): bool
    {
        return (bool)$this->sortByField;
    }

    /**
     * Handle dynamic method calls on the query builder object to be directed to the query processor.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        // We'll check if the beginning of the method being called contains
        // 'where'. If so, we'll assume it's a dynamic 'where' clause.
        if (str_starts_with($method, 'where')) {
            return $this->dynamicWhere($method, $parameters);
        }

        return call_user_func_array([$this->newProcessor(), $method], $parameters);
    }

    /**
     * Handles dynamic "where" clauses to the query.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return Builder
     */
    public function dynamicWhere(string $method, array $parameters): static
    {
        $finder = substr($method, 5);

        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1, PREG_SPLIT_DELIM_CAPTURE);

        // The connector variable will determine which connector will be used for the
        // query condition. We will change it as we come across new boolean values
        // in the dynamic method strings, which could contain a number of these.
        $connector = 'and';

        $index = 0;

        foreach ($segments as $segment) {
            // If the segment is not a boolean connector, we can assume it is a column's name
            // and we will add it to the query as a new constraint as a where clause, then
            // we can keep iterating through the dynamic method string's segments again.
            if ($segment != 'And' && $segment != 'Or') {
                $this->addDynamic($segment, $connector, $parameters, $index);

                $index++;
            }

            // Otherwise, we will store the connector so we know how the next where clause we
            // find in the query should be connected to the previous ones, meaning we will
            // have the proper boolean connector to connect the next where clause found.
            else {
                $connector = $segment;
            }
        }

        return $this;
    }

    /**
     * Adds an array of wheres to the current query.
     *
     * @param array $wheres
     * @param string $boolean
     * @param bool $raw
     *
     * @return Builder
     */
    protected function addArrayOfWheres(array $wheres, string $boolean, bool $raw): static
    {
        foreach ($wheres as $key => $value) {
            if (is_numeric($key) && is_array($value)) {
                // If the key is numeric and the value is an array, we'll
                // assume we've been given an array with conditionals.
                [$field, $condition] = $value;

                // Since a value is optional for some conditionals, we will
                // try and retrieve the third parameter from the array,
                // but is entirely optional.
                $value = Arr::get($value, 2);

                $this->where($field, $condition, $value, $boolean);
            } else {
                // If the value is not an array, we will assume an equals clause.
                $this->where($key, Operator::$equals, $value, $boolean, $raw);
            }
        }

        return $this;
    }

    /**
     * Add a single dynamic where clause statement to the query.
     *
     * @param string $segment
     * @param string $connector
     * @param array $parameters
     * @param int $index
     *
     * @return void
     */
    protected function addDynamic(string $segment, string $connector, array $parameters, int $index): void
    {
        // We'll format the 'where' boolean and field here to avoid casing issues.
        $bool = strtolower($connector);
        $field = strtolower($segment);

        $this->where($field, '=', $parameters[$index], $bool);
    }

    /**
     * Logs the given executed query information by firing its query event.
     *
     * @param Builder $query
     * @param string $type
     * @param float|null $time
     */
    protected function logQuery(Builder $query, string $type, ?float $time = null): void
    {
        $args = [$query, $time];

        $event = match ($type) {
            'listing' => new Events\Listing(...$args),
            'read' => new Events\Read(...$args),
            'paginate' => new Events\Paginate(...$args),
            default => new Events\Search(...$args),
        };

        $this->fireQueryEvent($event);
    }

    /**
     * Fires the given query event.
     *
     * @param QueryExecuted $event
     */
    protected function fireQueryEvent(QueryExecuted $event): void
    {
        Adldap::getEventDispatcher()->fire($event);
    }

    /**
     * Get the elapsed time since a given starting point.
     *
     * @param float $start
     *
     * @return float
     */
    protected function getElapsedTime(float $start): float
    {
        return round((microtime(true) - $start) * 1000, 2);
    }

    /**
     * Returns a new query Processor instance.
     *
     * @return Processor
     */
    protected function newProcessor(): Processor
    {
        return new Processor($this);
    }
}
