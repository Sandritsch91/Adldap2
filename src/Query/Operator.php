<?php

namespace Adldap\Query;

use ReflectionClass;

class Operator
{
    /**
     * The 'has' wildcard operator.
     *
     * @var string
     */
    public static string $has = '*';

    /**
     * The custom `notHas` operator.
     *
     * @var string
     */
    public static string $notHas = '!*';

    /**
     * The equals operator.
     *
     * @var string
     */
    public static string $equals = '=';

    /**
     * The does not equal operator.
     *
     * @var string
     */
    public static string $doesNotEqual = '!';

    /**
     * The does not equal operator (alias).
     *
     * @var string
     */
    public static string $doesNotEqualAlias = '!=';

    /**
     * The greater than or equal to operator.
     *
     * @var string
     */
    public static string $greaterThanOrEquals = '>=';

    /**
     * The less than or equal to operator.
     *
     * @var string
     */
    public static string $lessThanOrEquals = '<=';

    /**
     * The approximately equal to operator.
     *
     * @var string
     */
    public static string $approximatelyEquals = '~=';

    /**
     * The custom starts with operator.
     *
     * @var string
     */
    public static string $startsWith = 'starts_with';

    /**
     * The custom not starts with operator.
     *
     * @var string
     */
    public static string $notStartsWith = 'not_starts_with';

    /**
     * The custom ends with operator.
     *
     * @var string
     */
    public static string $endsWith = 'ends_with';

    /**
     * The custom not ends with operator.
     *
     * @var string
     */
    public static string $notEndsWith = 'not_ends_with';

    /**
     * The custom contains operator.
     *
     * @var string
     */
    public static string $contains = 'contains';

    /**
     * The custom not contains operator.
     *
     * @var string
     */
    public static string $notContains = 'not_contains';

    /**
     * Returns all available operators.
     *
     * @return array
     */
    public static function all(): array
    {
        return (new ReflectionClass(new static()))->getStaticProperties();
    }
}
