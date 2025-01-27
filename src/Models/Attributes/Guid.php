<?php

namespace Adldap\Models\Attributes;

use Adldap\Utilities;
use InvalidArgumentException;

class Guid
{
    /**
     * The string GUID value.
     *
     * @var string
     */
    protected mixed $value;

    /**
     * The guid structure in order by section to parse using substr().
     *
     * @author Chad Sikorra <Chad.Sikorra@gmail.com>
     *
     * @link https://github.com/ldaptools/ldaptools
     *
     * @var array
     */
    protected array $guidSections = [
        [[-26, 2], [-28, 2], [-30, 2], [-32, 2]],
        [[-22, 2], [-24, 2]],
        [[-18, 2], [-20, 2]],
        [[-16, 4]],
        [[-12, 12]],
    ];

    /**
     * The hexadecimal octet order based on string position.
     *
     * @author Chad Sikorra <Chad.Sikorra@gmail.com>
     *
     * @link https://github.com/ldaptools/ldaptools
     *
     * @var array
     */
    protected array $octetSections = [
        [6, 4, 2, 0],
        [10, 8],
        [14, 12],
        [16, 18, 20, 22, 24, 26, 28, 30],
    ];

    /**
     * Determines if the specified GUID is valid.
     *
     * @param string $guid
     *
     * @return bool
     */
    public static function isValid(string $guid): bool
    {
        return Utilities::isValidGuid($guid);
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
        } elseif ($value = $this->binaryGuidToString($value)) {
            $this->value = $value;
        } else {
            throw new InvalidArgumentException('Invalid Binary / String GUID.');
        }
    }

    /**
     * Returns the string value of the GUID.
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
     * Get the binary representation of the GUID string.
     *
     * @return string
     */
    public function getBinary(): string
    {
        $data = '';

        $guid = str_replace('-', '', $this->value);

        foreach ($this->octetSections as $section) {
            $data .= $this->parseSection($guid, $section, true);
        }

        return hex2bin($data);
    }

    /**
     * Returns the string variant of a binary GUID.
     *
     * @param string $binary
     *
     * @return string|null
     */
    protected function binaryGuidToString(string $binary): ?string
    {
        return Utilities::binaryGuidToString($binary);
    }

    /**
     * Return the specified section of the hexadecimal string.
     *
     * @param string $hex The full hex string.
     * @param array $sections An array of start and length (unless octet is true, then length is always 2).
     * @param bool $octet Whether this is for octet string form.
     *
     * @return string The concatenated sections in upper-case.
     * @author Chad Sikorra <Chad.Sikorra@gmail.com>
     *
     * @link https://github.com/ldaptools/ldaptools
     *
     */
    protected function parseSection(string $hex, array $sections, bool $octet = false): string
    {
        $parsedString = '';

        foreach ($sections as $section) {
            $start = $octet ? $section : $section[0];

            $length = $octet ? 2 : $section[1];

            $parsedString .= substr($hex, $start, $length);
        }

        return $parsedString;
    }
}
