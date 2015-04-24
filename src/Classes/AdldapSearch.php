<?php

namespace Adldap\Classes;

use Adldap\Exceptions\AdldapException;
use Adldap\Objects\LdapEntry;
use Adldap\Objects\LdapOperator;
use Adldap\Objects\Paginator;

/**
 * Class AdldapSearch.
 */
class AdldapSearch extends AdldapBase
{
    /**
     * Stores the current query string.
     *
     * @var string
     */
    protected $query = '';

    /**
     * Stores the distinguished name to search on.
     *
     * @var string
     */
    protected $dn = '';

    /**
     * Stores the bool to determine whether or not
     * to search LDAP recursively.
     *
     * @var bool
     */
    protected $recursive = true;

    /**
     * Stores the selects to use in the query when assembled.
     *
     * @var array
     */
    protected $selects = [];

    /**
     * Stores the wheres to use in the query when assembled.
     *
     * @var array
     */
    protected $wheres = [];

    /**
     * Stores the orWheres to use in the query
     * when assembled.
     *
     * @var array
     */
    protected $orWheres = [];

    /**
     * Stores the field to sort search results by.
     *
     * @var string
     */
    protected $sortByField = '';

    /**
     * Stores the direction to sort the search results by.
     *
     * @var string
     */
    protected $sortByDirection = 'DESC';

    /**
     * The opening query string.
     *
     * @var string
     */
    protected static $openQuery = '(';

    /**
     * The closing query string.
     *
     * @var string
     */
    protected static $closeQuery = ')';

    /**
     * Performs the specified query on the current LDAP connection.
     *
     * @param string $query
     *
     * @return bool|array
     */
    public function query($query)
    {
        // If the query is empty, we'll return false
        if ($query === null || empty($query)) {
            return false;
        }

        /*
         * If the search is recursive, we'll run a search,
         * if not, we'll run a listing.
         */
        if ($this->recursive) {
            $results = $this->connection->search($this->getDn(), $query, $this->getSelects());
        } else {
            $results = $this->connection->listing($this->getDn(), $query, $this->getSelects());
        }

        if ($results) {
            return $this->processResults($results);
        }

        return false;
    }

    /**
     * Performs the current query on the current LDAP connection.
     *
     * @return array|bool
     */
    public function get()
    {
        return $this->query($this->getQuery(), $this->getSelects());
    }

    /**
     * Performs a global 'all' search query on the
     * current connection.
     *
     * @return array|bool
     */
    public function all()
    {
        $this->where('objectClass', LdapOperator::$wildcard);

        return $this->get();
    }

    /**
     * Paginates the current LDAP query.
     *
     * @param int  $perPage
     * @param int  $currentPage
     * @param bool $isCritical
     *
     * @return bool
     */
    public function paginate($perPage = 50, $currentPage = 0, $isCritical = true)
    {
        // Stores all LDAP entries in a page array
        $pages = [];

        $cookie = '';

        do {
            $this->connection->controlPagedResult($perPage, $isCritical, $cookie);

            $results = $this->connection->search($this->adldap->getBaseDn(), $this->getQuery(), $this->getSelects());

            if ($results) {
                $this->connection->controlPagedResultResponse($results, $cookie);

                $pages[] = $results;
            }
        } while ($cookie !== null && ! empty($cookie));

        if (count($pages) > 0) {
            return $this->processPaginatedResults($pages, $perPage, $currentPage);
        }

        return false;
    }

    /**
     * Returns the first entry in a search result.
     *
     * @return array|bool
     */
    public function first()
    {
        $results = $this->get();

        if (is_array($results) && array_key_exists(0, $results)) {
            return $results[0];
        }

        return $results;
    }

    /**
     * Adds the inserted fields to query on the current LDAP connection.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function select($fields = [])
    {
        if (is_array($fields)) {
            foreach ($fields as $field) {
                $this->addSelect($field);
            }
        } elseif (is_string($fields)) {
            $this->addSelect($fields);
        }

        return $this;
    }

    /**
     * Adds a where clause to the current query.
     *
     * @param $field
     * @param null $operator
     * @param null $value
     *
     * @return $this
     */
    public function where($field, $operator = null, $value = null)
    {
        $this->addWhere($field, $operator, $value);

        return $this;
    }

    /**
     * Adds an orWhere clause to the current query.
     *
     * @param string $field
     * @param null   $operator
     * @param null   $value
     *
     * @return $this
     */
    public function orWhere($field, $operator = null, $value = null)
    {
        $this->addOrWhere($field, $operator, $value);

        return $this;
    }

    /**
     * Returns true / false depending if the current object
     * contains selects.
     *
     * @return bool
     */
    public function hasSelects()
    {
        if (count($this->selects) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Returns the current selected fields to retrieve.
     *
     * @return array
     */
    public function getSelects()
    {
        return $this->selects;
    }

    /**
     * Returns the wheres on the current search object.
     *
     * @return array
     */
    public function getWheres()
    {
        return $this->wheres;
    }

    /**
     * Returns the or wheres on the current search object.
     *
     * @return array
     */
    public function getOrWheres()
    {
        return $this->orWheres;
    }

    /**
     * Returns the current LDAP query string.
     *
     * @return string
     */
    public function getQuery()
    {
        // Return the query if it exists
        if (! empty($this->query)) {
            return $this->query;
        }

        /*
         * Looks like our query hasn't been assembled
         * yet, let's try to assemble it
         */
        $this->assembleQuery();

        // Return the assembled query
        return $this->query;
    }

    /**
     * Sorts the LDAP search results by the specified field
     * and direction.
     *
     * @param $field
     * @param string $direction
     *
     * @return $this
     */
    public function sortBy($field, $direction = 'desc')
    {
        $this->sortByField = $field;

        if (strtolower($direction) === 'asc') {
            $this->sortByDirection = SORT_ASC;
        } else {
            $this->sortByDirection = SORT_DESC;
        }

        return $this;
    }

    /**
     * Sets the complete distinguished name to search on.
     *
     * @param string $dn
     *
     * @return $this
     */
    public function setDn($dn)
    {
        $this->dn = (string) $dn;

        return $this;
    }

    /**
     * Returns the current distinguished name.
     *
     * This will return the domains base DN if a search
     * DN is not set.
     *
     * @return string
     */
    public function getDn()
    {
        if (empty($this->dn)) {
            return $this->adldap->getBaseDn();
        }

        return $this->dn;
    }

    /**
     * Sets the recursive property to tell the search
     * whether or not to search recursively.
     *
     * @param bool $recursive
     *
     * @return $this
     */
    public function recursive($recursive = true)
    {
        $this->recursive = true;

        if ($recursive === false) {
            $this->recursive = false;
        }

        return $this;
    }

    /**
     * Adds the inserted field to the selects property.
     *
     * @param string $field
     */
    private function addSelect($field)
    {
        $this->selects[] = $field;
    }

    /**
     * Adds the inserted field, operator and value
     * to the wheres property array.
     *
     * @param string $field
     * @param string $operator
     * @param null   $value
     *
     * @throws AdldapException
     */
    private function addWhere($field, $operator, $value = null)
    {
        $this->wheres[] = [
            'field' => $field,
            'operator' => $this->getOperator($operator),
            'value' => $this->connection->escape($value),
        ];
    }

    /**
     * Adds the inserted field, operator and value
     * to the orWheres property array.
     *
     * @param string $field
     * @param string $operator
     * @param null   $value
     *
     * @throws AdldapException
     */
    private function addOrWhere($field, $operator, $value = null)
    {
        $this->orWheres[] = [
            'field' => $field,
            'operator' => $this->getOperator($operator),
            'value' => $this->connection->escape($value),
        ];
    }

    /**
     * Sets the query property.
     *
     * @param string $query
     */
    private function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * Adds the specified query onto the current query.
     *
     * @param string $query
     */
    private function addToQuery($query)
    {
        $this->query .= $query;
    }

    /**
     * Returns an assembled query using the current object parameters.
     *
     * @return string
     */
    private function assembleQuery()
    {
        $this->assembleWheres();

        $this->assembleOrWheres();

        /*
         * Make sure we wrap the query in an 'and'
         * if using multiple wheres or if we have any
         * orWheres. For example (&(cn=John*)(|(description=User*)))
         */
        if (count($this->getWheres()) > 1 || count($this->getOrWheres()) > 0) {
            $this->setQuery($this->queryAnd($this->getQuery()));
        }
    }

    /**
     * Assembles all where clauses in the current wheres property.
     */
    private function assembleWheres()
    {
        if (count($this->wheres) > 0) {
            foreach ($this->wheres as $where) {
                $this->addToQuery($this->assembleWhere($where));
            }
        }
    }

    /**
     * Assembles all or where clauses in the current orWheres property.
     */
    private function assembleOrWheres()
    {
        if (count($this->orWheres) > 0) {
            $ors = '';

            foreach ($this->orWheres as $where) {
                $ors .= $this->assembleWhere($where);
            }

            /*
             * Make sure we wrap the query in an 'and'
             * if using multiple wheres. For example (&QUERY)
             */
            if (count($this->orWheres) > 0) {
                $this->addToQuery($this->queryOr($ors));
            }
        }
    }

    /**
     * Assembles a single where query based
     * on its operator and returns it.
     *
     * @param array $where
     *
     * @return string|null
     */
    private function assembleWhere($where = array())
    {
        if(is_array($where))
        {
            switch ($where['operator']) {
                case LdapOperator::$equals:
                    return $this->queryEquals($where['field'], $where['value']);
                case LdapOperator::$doesNotEqual:
                    return $this->queryDoesNotEqual($where['field'], $where['value']);
                case LdapOperator::$greaterThanOrEqual:
                    return $this->queryGreaterThanOrEquals($where['field'], $where['value']);
                case LdapOperator::$lessThanOrEqual:
                    return $this->queryLessThanOrEquals($where['field'], $where['value']);
                case LdapOperator::$approximateEqual:
                    return $this->queryApproximatelyEquals($where['field'], $where['value']);
                case LdapOperator::$wildcard:
                    return $this->queryWildcard($where['field']);
            }
        }

        return null;
    }

    /**
     * Returns a query string for does not equal.
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    private function queryDoesNotEqual($field, $value)
    {
        return $this::$openQuery.LdapOperator::$doesNotEqual.$this->queryEquals($field, $value).$this::$closeQuery;
    }

    /**
     * Returns a query string for equals.
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    private function queryEquals($field, $value)
    {
        return $this::$openQuery.$field.LdapOperator::$equals.$value.$this::$closeQuery;
    }

    /**
     * Returns a query string for greater than or equals.
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    private function queryGreaterThanOrEquals($field, $value)
    {
        return $this::$openQuery.$field.LdapOperator::$greaterThanOrEqual.$value.$this::$closeQuery;
    }

    /**
     * Returns a query string for less than or equals.
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    private function queryLessThanOrEquals($field, $value)
    {
        return $this::$openQuery.$field.LdapOperator::$lessThanOrEqual.$value.$this::$closeQuery;
    }

    /**
     * Returns a query string for approximately equals.
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    private function queryApproximatelyEquals($field, $value)
    {
        return $this::$openQuery.$field.LdapOperator::$approximateEqual.$value.$this::$closeQuery;
    }

    /**
     * Returns a query string for a wildcard.
     *
     * @param string $field
     *
     * @return string
     */
    private function queryWildcard($field)
    {
        return $this::$openQuery.$field.LdapOperator::$equals.LdapOperator::$wildcard.$this::$closeQuery;
    }

    /**
     * Wraps the inserted query inside an AND operator.
     *
     * @param string $query
     *
     * @return string
     */
    private function queryAnd($query)
    {
        return $this::$openQuery.LdapOperator::$and.$query.$this::$closeQuery;
    }

    /**
     * Wraps the inserted query inside an OR operator.
     *
     * @param string $query
     *
     * @return string
     */
    private function queryOr($query)
    {
        return $this::$openQuery.LdapOperator::$or.$query.$this::$closeQuery;
    }

    /**
     * Retrieves an operator from the available operators.
     *
     * Throws an AdldapException if no operator is found.
     *
     * @param $operator
     *
     * @return string
     *
     * @throws AdldapException
     */
    private function getOperator($operator)
    {
        $operators = $this->getOperators();

        $key = array_search($operator, $operators);

        if ($key !== false && array_key_exists($key, $operators)) {
            return $operators[$key];
        }

        $operators = implode(', ', $operators);

        $message = "Operator: $operator cannot be used in an LDAP query. Available operators are $operators";

        throw new AdldapException($message);
    }

    /**
     * Returns an array of available operators.
     *
     * @return array
     */
    private function getOperators()
    {
        return [
            LdapOperator::$wildcard,
            LdapOperator::$equals,
            LdapOperator::$doesNotEqual,
            LdapOperator::$greaterThanOrEqual,
            LdapOperator::$lessThanOrEqual,
            LdapOperator::$approximateEqual,
            LdapOperator::$and,
        ];
    }

    /**
     * Processes LDAP search results into a nice array.
     *
     * @param resource $results
     *
     * @return array
     */
    private function processResults($results)
    {
        $entries = $this->connection->getEntries($results);

        $objects = [];

        if (array_key_exists('count', $entries)) {
            for ($i = 0; $i < $entries['count']; $i++) {
                $entry = new LdapEntry($entries[$i], $this->connection);

                $objects[] = $entry->getAttributes();
            }

            if (! empty($this->sortByField)) {
                return $this->processSortBy($objects);
            }
        }

        return $objects;
    }

    /**
     * Processes paginated LDAP results.
     *
     * @param array $pages
     * @param int   $perPage
     * @param int   $currentPage
     *
     * @return array|bool
     */
    private function processPaginatedResults($pages, $perPage = 50, $currentPage = 0)
    {
        // Make sure we have at least one page of results
        if (count($pages) > 0) {
            $objects = [];

            // Go through each page
            foreach ($pages as $results) {
                // Get the entries for each page
                $entries = $this->connection->getEntries($results);

                /*
                 * If we've retrieved entries, we'll go through
                 * each and construct the entry attributes, and
                 * put them all inside the objects array
                 */
                if (is_array($entries) && array_key_exists('count', $entries)) {
                    for ($i = 0; $i < $entries['count']; $i++) {
                        $entry = new LdapEntry($entries[$i], $this->connection);

                        $objects[] = $entry->getAttributes();
                    }
                }
            }

            /*
             * If we're sorting, we'll process all of
             * our results so it's sorted correctly
             */
            if (! empty($this->sortByField)) {
                $objects = $this->processSortBy($objects);
            }

            // Return a new paginator instance
            return new Paginator($objects, $perPage, $currentPage, count($pages));
        }

        // Looks like we don't have any results, return false
        return false;
    }

    /**
     * Processes the array of specified object results
     * and sorts them by the field and direction search
     * property.
     *
     * @param $objects
     * @param array
     */
    private function processSortBy($objects)
    {
        if (count($objects) > 0) {
            foreach ($objects as $key => $row) {
                if (array_key_exists($this->sortByField, $row)) {
                    $sort[$key] = $row[$this->sortByField];
                }
            }

            array_multisort($sort, $this->sortByDirection, $objects);
        }

        return $objects;
    }
}
