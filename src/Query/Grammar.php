<?php

namespace Adldap\Query;

class Grammar
{
    /**
     * Wraps a query string in brackets.
     *
     * Produces: (query)
     *
     * @param string $query
     * @param string|null $prefix
     * @param string|null $suffix
     *
     * @return string
     */
    public function wrap(string $query, ?string $prefix = '(', ?string $suffix = ')'): string
    {
        return $prefix . $query . $suffix;
    }

    /**
     * Compiles the Builder instance into an LDAP query string.
     *
     * @param Builder $builder
     *
     * @return string
     */
    public function compile(Builder $builder): string
    {
        $ands = $builder->filters['and'];
        $ors = $builder->filters['or'];
        $raws = $builder->filters['raw'];

        $query = $this->concatenate($raws);

        $query = $this->compileWheres($ands, $query);

        $query = $this->compileOrWheres($ors, $query);

        // We need to check if the query is already nested, otherwise
        // we'll nest it here and return the result.
        if (!$builder->isNested()) {
            $total = count($ands) + count($raws);

            // Make sure we wrap the query in an 'and' if using
            // multiple filters. We also need to check if only
            // one where is used with multiple orWheres, that
            // we wrap it in an `and` query.
            if ($total > 1 || (count($ands) === 1 && count($ors) > 0)) {
                $query = $this->compileAnd($query);
            }
        }

        return $query;
    }

    /**
     * Concatenates filters into a single string.
     *
     * @param array $bindings
     *
     * @return string
     */
    public function concatenate(array $bindings = []): string
    {
        // Filter out empty query segments.
        $bindings = array_filter($bindings, function ($value) {
            return (string)$value !== '';
        });

        return implode('', $bindings);
    }

    /**
     * Returns a query string for equals.
     *
     * Produces: (field=value)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileEquals(string $field, string $value): string
    {
        return $this->wrap($field . Operator::$equals . $value);
    }

    /**
     * Returns a query string for does not equal.
     *
     * Produces: (!(field=value))
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileDoesNotEqual(string $field, string $value): string
    {
        return $this->compileNot($this->compileEquals($field, $value));
    }

    /**
     * Alias for does not equal operator (!=) operator.
     *
     * Produces: (!(field=value))
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileDoesNotEqualAlias(string $field, string $value): string
    {
        return $this->compileDoesNotEqual($field, $value);
    }

    /**
     * Returns a query string for greater than or equals.
     *
     * Produces: (field>=value)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileGreaterThanOrEquals(string $field, string $value): string
    {
        return $this->wrap($field . Operator::$greaterThanOrEquals . $value);
    }

    /**
     * Returns a query string for less than or equals.
     *
     * Produces: (field<=value)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileLessThanOrEquals(string $field, string $value): string
    {
        return $this->wrap($field . Operator::$lessThanOrEquals . $value);
    }

    /**
     * Returns a query string for approximately equals.
     *
     * Produces: (field~=value)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileApproximatelyEquals(string $field, string $value): string
    {
        return $this->wrap($field . Operator::$approximatelyEquals . $value);
    }

    /**
     * Returns a query string for starts with.
     *
     * Produces: (field=value*)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileStartsWith(string $field, string $value): string
    {
        return $this->wrap($field . Operator::$equals . $value . Operator::$has);
    }

    /**
     * Returns a query string for does not start with.
     *
     * Produces: (!(field=*value))
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileNotStartsWith(string $field, string $value): string
    {
        return $this->compileNot($this->compileStartsWith($field, $value));
    }

    /**
     * Returns a query string for ends with.
     *
     * Produces: (field=*value)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileEndsWith(string $field, string $value): string
    {
        return $this->wrap($field . Operator::$equals . Operator::$has . $value);
    }

    /**
     * Returns a query string for does not end with.
     *
     * Produces: (!(field=value*))
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileNotEndsWith(string $field, string $value): string
    {
        return $this->compileNot($this->compileEndsWith($field, $value));
    }

    /**
     * Returns a query string for contains.
     *
     * Produces: (field=*value*)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileContains(string $field, string $value): string
    {
        return $this->wrap($field . Operator::$equals . Operator::$has . $value . Operator::$has);
    }

    /**
     * Returns a query string for does not contain.
     *
     * Produces: (!(field=*value*))
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileNotContains(string $field, string $value): string
    {
        return $this->compileNot($this->compileContains($field, $value));
    }

    /**
     * Returns a query string for a where has.
     *
     * Produces: (field=*)
     *
     * @param string $field
     *
     * @return string
     */
    public function compileHas(string $field): string
    {
        return $this->wrap($field . Operator::$equals . Operator::$has);
    }

    /**
     * Returns a query string for a where does not have.
     *
     * Produces: (!(field=*))
     *
     * @param string $field
     *
     * @return string
     */
    public function compileNotHas(string $field): string
    {
        return $this->compileNot($this->compileHas($field));
    }

    /**
     * Wraps the inserted query inside an AND operator.
     *
     * Produces: (&query)
     *
     * @param string $query
     *
     * @return string
     */
    public function compileAnd(string $query): string
    {
        return $query ? $this->wrap($query, '(&') : '';
    }

    /**
     * Wraps the inserted query inside an OR operator.
     *
     * Produces: (|query)
     *
     * @param string $query
     *
     * @return string
     */
    public function compileOr(string $query): string
    {
        return $query ? $this->wrap($query, '(|') : '';
    }

    /**
     * Wraps the inserted query inside an NOT operator.
     *
     * @param string $query
     *
     * @return string
     */
    public function compileNot(string $query): string
    {
        return $query ? $this->wrap($query, '(!') : '';
    }

    /**
     * Assembles all where clauses in the current wheres property.
     *
     * @param array $wheres
     * @param string $query
     *
     * @return string
     */
    protected function compileWheres(array $wheres = [], string $query = ''): string
    {
        foreach ($wheres as $where) {
            $query .= $this->compileWhere($where);
        }

        return $query;
    }

    /**
     * Assembles all or where clauses in the current orWheres property.
     *
     * @param array $orWheres
     * @param string $query
     *
     * @return string
     */
    protected function compileOrWheres(array $orWheres = [], string $query = ''): string
    {
        $or = '';

        foreach ($orWheres as $where) {
            $or .= $this->compileWhere($where);
        }

        // Make sure we wrap the query in an 'or' if using multiple
        // orWheres. For example (|(QUERY)(ORWHEREQUERY)).
        if (($query && count($orWheres) > 0) || count($orWheres) > 1) {
            $query .= $this->compileOr($or);
        } else {
            $query .= $or;
        }

        return $query;
    }

    /**
     * Assembles a single where query based
     * on its operator and returns it.
     *
     * @param array $where
     *
     * @return string|null
     */
    protected function compileWhere(array $where): ?string
    {
        // Get the name of the operator.
        if ($name = array_search($where['operator'], Operator::all())) {
            // If the name was found we'll camel case it
            // to run it through the compile method.
            $method = 'compile' . ucfirst($name);

            // Make sure the compile method exists for the operator.
            if (method_exists($this, $method)) {
                return $this->{$method}($where['field'], $where['value']);
            }
        }
        return null;
    }
}
