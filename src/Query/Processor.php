<?php

namespace Adldap\Query;

use Adldap\Connections\ConnectionInterface;
use Adldap\Models\Entry;
use Adldap\Models\Model;
use Adldap\Schemas\SchemaInterface;
use InvalidArgumentException;

class Processor
{
    /**
     * @var Builder
     */
    protected Builder $builder;

    /**
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;

    /**
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
        $this->builder = $builder;
        $this->schema = $builder->getSchema();
        $this->connection = $builder->getConnection();
    }

    /**
     * Processes LDAP search results and constructs their model instances.
     *
     * @param array|Model $entries The LDAP entries to process.
     *
     * @return Collection|array
     */
    public function process(array|Model $entries): array|Collection
    {
        if ($this->builder->isRaw()) {
            // If the builder is asking for a raw
            // LDAP result, we can return here.
            return $entries;
        }

        $models = [];

        if (is_array($entries) && array_key_exists('count', $entries)) {
            for ($i = 0; $i < $entries['count']; $i++) {
                // We'll go through each entry and construct a new
                // model instance with the raw LDAP attributes.
                $models[] = $this->newLdapEntry($entries[$i]);
            }
        }

        // If the query contains paginated results, we'll return them here.
        if ($this->builder->isPaginated()) {
            return $models;
        }

        // If the query is requested to be sorted, we'll perform
        // that here and return the resulting collection.
        if ($this->builder->isSorted()) {
            return $this->processSort($models);
        }

        // Otherwise, we'll return a regular unsorted collection.
        return $this->newCollection($models);
    }

    /**
     * Processes paginated LDAP results.
     *
     * @param array $pages
     * @param int $perPage
     * @param int $currentPage
     *
     * @return Paginator
     */
    public function processPaginated(array $pages = [], int $perPage = 50, int $currentPage = 0): Paginator
    {
        $models = [];

        foreach ($pages as $entries) {
            // Go through each page and process the results into an objects array.
            $models = array_merge($models, $this->process($entries));
        }

        $models = $this->processSort($models)->toArray();

        return $this->newPaginator($models, $perPage, $currentPage, count($pages));
    }

    /**
     * Returns a new LDAP Entry instance.
     *
     * @param array $attributes
     *
     * @return Entry
     */
    public function newLdapEntry(array $attributes = []): Entry
    {
        $objectClass = $this->schema->objectClass();

        // We need to ensure the record contains an object class to be able to
        // determine its type. Otherwise, we create a default Entry model.
        if (array_key_exists($objectClass, $attributes) && array_key_exists(0, $attributes[$objectClass])) {
            // Retrieve all of the object classes from the LDAP
            // entry and lowercase them for comparisons.
            $classes = array_map('strtolower', $attributes[$objectClass]);

            // Retrieve the model mapping.
            $models = $this->schema->objectClassModelMap();

            // Retrieve the object class mappings (with strtolower keys).
            $mappings = array_map('strtolower', array_keys($models));

            // Retrieve the model from the map using the entry's object class.
            $map = array_intersect($mappings, $classes);

            if (count($map) > 0) {
                // Retrieve the model using the object class.
                $model = $models[current($map)];

                // Construct and return a new model.
                return $this->newModel([], $model)
                    ->setRawAttributes($attributes);
            }
        }

        // A default entry model if the object class isn't found.
        return $this->newModel()->setRawAttributes($attributes);
    }

    /**
     * Creates a new model instance.
     *
     * @param array $attributes
     * @param string|null $model
     *
     * @return mixed|Entry
     * @throws InvalidArgumentException
     *
     */
    public function newModel(array $attributes = [], ?string $model = null): mixed
    {
        $model = ($model !== null && class_exists($model) ? $model : $this->schema->entryModel());

        if (!is_subclass_of($model, $base = Model::class)) {
            throw new InvalidArgumentException("The given model class '$model' must extend the base model class '$base'");
        }

        return new $model($attributes, $this->builder->newInstance());
    }

    /**
     * Returns a new Paginator object instance.
     *
     * @param array $models
     * @param int $perPage
     * @param int $currentPage
     * @param int $pages
     *
     * @return Paginator
     */
    public function newPaginator(array $models = [], int $perPage = 25, int $currentPage = 0, int $pages = 1): Paginator
    {
        return new Paginator($models, $perPage, $currentPage, $pages);
    }

    /**
     * Returns a new collection instance.
     *
     * @param array $items
     *
     * @return Collection
     */
    public function newCollection(array $items = []): Collection
    {
        return new Collection($items);
    }

    /**
     * Sorts LDAP search results.
     *
     * @param array $models
     *
     * @return Collection
     */
    protected function processSort(array $models = []): Collection
    {
        $field = $this->builder->getSortByField();

        $flags = $this->builder->getSortByFlags() ?? \SORT_REGULAR;

        $direction = $this->builder->getSortByDirection();

        $desc = $direction === 'desc';

        return $this->newCollection($models)->sortBy($field, $flags, $desc);
    }
}
