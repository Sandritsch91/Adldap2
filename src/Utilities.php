<?php

namespace Adldap;

class Utilities
{
    /**
     * Converts a DN string into an array of RDNs.
     *
     * This will also decode hex characters into their true
     * UTF-8 representation embedded inside the DN as well.
     *
     * @param string $dn
     * @param bool $removeAttributePrefixes
     *
     * @return array|false
     */
    public static function explodeDn(string $dn, bool $removeAttributePrefixes = true): bool|array
    {
        $dn = ldap_explode_dn($dn, ($removeAttributePrefixes ? 1 : 0));

        if (is_array($dn) && array_key_exists('count', $dn)) {
            foreach ($dn as $rdn => $value) {
                $dn[$rdn] = self::unescape($value);
            }
        }

        return $dn;
    }

    /**
     * Un-escapes a hexadecimal string into
     * its original string representation.
     *
     * @param string $value
     *
     * @return string
     */
    public static function unescape(string $value): string
    {
        return preg_replace_callback('/\\\([0-9A-Fa-f]{2})/', function ($matches) {
            return chr(hexdec($matches[1]));
        }, $value);
    }

    /**
     * Convert a binary SID to a string SID.
     *
     * @param string $value The Binary SID
     *
     * @return string|null
     * @link https://stackoverflow.com/questions/39533560/php-ldap-get-user-sid
     *
     * @author Chad Sikorra
     *
     * @link https://github.com/ChadSikorra
     */
    public static function binarySidToString(string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Revision - 8bit unsigned int (C1)
        // Count - 8bit unsigned int (C1)
        // 2 null bytes
        // ID - 32bit unsigned long, big-endian order
        $sid = @unpack('C1rev/C1count/x2/N1id', $value);

        if (!isset($sid['id']) || !isset($sid['rev'])) {
            return null;
        }

        $revisionLevel = $sid['rev'];

        $identifierAuthority = $sid['id'];

        $subs = $sid['count'] ?? 0;

        $sidHex = $subs ? bin2hex($value) : '';

        $subAuthorities = [];

        // The sub-authorities depend on the count, so only get as
        // many as the count, regardless of data beyond it.
        for ($i = 0; $i < $subs; $i++) {
            $data = implode('', array_reverse(
                str_split(
                    substr($sidHex, 16 + ($i * 8), 8),
                    2
                )
            ));

            $subAuthorities[] = hexdec($data);
        }

        // Tack on the 'S-' and glue it all together...
        return 'S-' . $revisionLevel . '-' . $identifierAuthority . implode(
                preg_filter('/^/', '-', $subAuthorities)
            );
    }

    /**
     * Convert a binary GUID to a string GUID.
     *
     * @param string $binGuid
     *
     * @return string|null
     */
    public static function binaryGuidToString(string $binGuid): ?string
    {
        if (trim($binGuid) === '') {
            return null;
        }

        $hex = unpack('H*hex', $binGuid)['hex'];

        $hex1 = substr($hex, -26, 2) . substr($hex, -28, 2) . substr($hex, -30, 2) . substr($hex, -32, 2);
        $hex2 = substr($hex, -22, 2) . substr($hex, -24, 2);
        $hex3 = substr($hex, -18, 2) . substr($hex, -20, 2);
        $hex4 = substr($hex, -16, 4);
        $hex5 = substr($hex, -12, 12);

        return sprintf('%s-%s-%s-%s-%s', $hex1, $hex2, $hex3, $hex4, $hex5);
    }

    /**
     * Converts a string GUID to it's hex variant.
     *
     * @param string $string
     *
     * @return string
     */
    public static function stringGuidToHex(string $string): string
    {
        $hex = '\\' . substr($string, 6, 2) . '\\' . substr($string, 4, 2) . '\\' . substr($string, 2,
                2) . '\\' . substr($string, 0, 2);
        $hex = $hex . '\\' . substr($string, 11, 2) . '\\' . substr($string, 9, 2);
        $hex = $hex . '\\' . substr($string, 16, 2) . '\\' . substr($string, 14, 2);
        $hex = $hex . '\\' . substr($string, 19, 2) . '\\' . substr($string, 21, 2);
        return $hex . '\\' . substr($string, 24, 2) . '\\' . substr($string, 26, 2) . '\\' . substr($string, 28,
                2) . '\\' . substr($string, 30, 2) . '\\' . substr($string, 32, 2) . '\\' . substr($string, 34, 2);
    }

    /**
     * Encode a password for transmission over LDAP.
     *
     * @param string $password The password to encode
     *
     * @return string
     */
    public static function encodePassword(string $password): string
    {
        return iconv('UTF-8', 'UTF-16LE', '"' . $password . '"');
    }

    /**
     * Salt and hash a password to make its SSHA OpenLDAP version.
     *
     * @param string $password The password to create
     *
     * @return string
     */
    public static function makeSSHAPassword(string $password): string
    {
        mt_srand((float)microtime() * 1000000);
        $salt = pack('CCCC', mt_rand(), mt_rand(), mt_rand(), mt_rand());

        return '{SSHA}' . base64_encode(pack('H*', sha1($password . $salt)) . $salt);
    }

    /**
     * Round a Windows timestamp down to seconds and remove
     * the seconds between 1601-01-01 and 1970-01-01.
     *
     * @param float $windowsTime
     *
     * @return float|int
     */
    public static function convertWindowsTimeToUnixTime(float $windowsTime): float|int
    {
        return round($windowsTime / 10000000) - 11644473600;
    }

    /**
     * Convert a Unix timestamp to Windows timestamp.
     *
     * @param float $unixTime
     *
     * @return float
     */
    public static function convertUnixTimeToWindowsTime(float $unixTime): float
    {
        return ($unixTime + 11644473600) * 10000000;
    }

    /**
     * Validates that the inserted string is an object SID.
     *
     * @param string $sid
     *
     * @return bool
     */
    public static function isValidSid(string $sid): bool
    {
        return (bool)preg_match("/^S-\d(-\d{1,10}){1,16}$/i", $sid);
    }

    /**
     * Validates that the inserted string is an object GUID.
     *
     * @param string $guid
     *
     * @return bool
     */
    public static function isValidGuid(string $guid): bool
    {
        return (bool)preg_match(
            '/^([0-9a-fA-F]){8}(-([0-9a-fA-F]){4}){3}-([0-9a-fA-F]){12}$|^([0-9a-fA-F]{8}-){3}[0-9a-fA-F]{8}$/',
            $guid
        );
    }

    /**
     * Converts an ignore string into an array.
     *
     * @param string $ignore
     *
     * @return array
     */
    protected static function ignoreStrToArray(string $ignore): array
    {
        $ignore = trim($ignore);

        return $ignore ? str_split($ignore) : [];
    }
}
