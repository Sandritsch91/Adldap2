<?php

namespace Adldap\Models\Attributes;

use Adldap\Utilities;
use InvalidArgumentException;

class Sid
{
    /**
     * The string SID value.
     *
     * @var string
     */
    protected string $value;

    /**
     * Determines if the specified SID is valid.
     *
     * @param string $sid
     *
     * @return bool
     */
    public static function isValid(string $sid): bool
    {
        return Utilities::isValidSid($sid);
    }

    /**
     * Constructor.
     *
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     */
    public function __construct(mixed $value)
    {
        if (static::isValid($value)) {
            $this->value = $value;
        } elseif ($value = $this->binarySidToString($value)) {
            $this->value = $value;
        } else {
            throw new InvalidArgumentException('Invalid Binary / String SID.');
        }
    }

    /**
     * Returns the string value of the SID.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }

    /**
     * Returns the string value of the SID.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Returns the binary variant of the SID.
     *
     * @return string
     */
    public function getBinary(): string
    {
        $sid = explode('-', ltrim($this->value, 'S-'));

        $level = (int)array_shift($sid);

        $authority = (int)array_shift($sid);

        $subAuthorities = array_map('intval', $sid);

        $params = array_merge(
            ['C2xxNV*', $level, count($subAuthorities), $authority],
            $subAuthorities
        );

        return call_user_func_array('pack', $params);
    }

    /**
     * Returns the string variant of a binary SID.
     *
     * @param string $binary
     *
     * @return string|null
     */
    protected function binarySidToString(string $binary): ?string
    {
        return Utilities::binarySidToString($binary);
    }
}
