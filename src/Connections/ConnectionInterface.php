<?php

namespace Adldap\Connections;

use LDAP\Connection;
use LDAP\Result;

/**
 * The Connection interface used for making connections. Implementing
 * this interface on connection classes helps unit and functional
 * test classes that require a connection.
 *
 * Interface ConnectionInterface
 */
interface ConnectionInterface
{
    /**
     * The SSL LDAP protocol string.
     *
     * @var string
     */
    const PROTOCOL_SSL = 'ldaps://';

    /**
     * The standard LDAP protocol string.
     *
     * @var string
     */
    const PROTOCOL = 'ldap://';

    /**
     * The LDAP SSL port number.
     *
     * @var string
     */
    const PORT_SSL = 636;

    /**
     * The standard LDAP port number.
     *
     * @var string
     */
    const PORT = 389;

    /**
     * Constructor.
     *
     * @param string|null $name The connection name.
     */
    public function __construct(?string $name = null);

    /**
     * Returns true / false if the current connection instance is using SSL.
     *
     * @return bool
     */
    public function isUsingSSL(): bool;

    /**
     * Returns true / false if the current connection instance is using TLS.
     *
     * @return bool
     */
    public function isUsingTLS(): bool;

    /**
     * Returns true / false if the current connection is able to modify passwords.
     *
     * @return bool
     */
    public function canChangePasswords(): bool;

    /**
     * Returns true / false if the current connection is bound.
     *
     * @return bool
     */
    public function isBound(): bool;

    /**
     * Sets the current connection to use SSL.
     *
     * @param bool $enabled
     *
     * @return ConnectionInterface
     */
    public function ssl(bool $enabled = true): ConnectionInterface;

    /**
     * Sets the current connection to use TLS.
     *
     * @param bool $enabled
     *
     * @return ConnectionInterface
     */
    public function tls(bool $enabled = true): ConnectionInterface;

    /**
     * Returns the full LDAP host URL.
     *
     * Ex: ldap://192.168.1.1:386
     *
     * @return string|null
     */
    public function getHost(): ?string;

    /**
     * Returns the connections name.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Get the current connection.
     *
     * @return Connection|false|null
     */
    public function getConnection(): Connection|false|null;

    /**
     * Retrieve the entries from a search result.
     *
     * @link http://php.net/manual/en/function.ldap-get-entries.php
     *
     * @param Result $searchResults
     *
     * @return mixed
     */
    // todo - add return type Result
    public function getEntries(mixed $searchResults): mixed;

    /**
     * Returns the number of entries from a search result.
     *
     * @link http://php.net/manual/en/function.ldap-count-entries.php
     *
     * @param Result $searchResults
     *
     * @return int
     */
    public function countEntries(Result $searchResults): int;

    /**
     * Compare value of attribute found in entry specified with DN.
     *
     * @link http://php.net/manual/en/function.ldap-compare.php
     *
     * @param string $dn
     * @param string $attribute
     * @param string $value
     *
     * @return int|bool
     */
    public function compare(string $dn, string $attribute, string $value): int|bool;

    /**
     * Retrieves the first entry from a search result.
     *
     * @link http://php.net/manual/en/function.ldap-first-entry.php
     *
     * @param Result $searchResults
     *
     * @return array|false
     */
    public function getFirstEntry(Result $searchResults): array|false;

    /**
     * Retrieves the next entry from a search result.
     *
     * @link http://php.net/manual/en/function.ldap-next-entry.php
     *
     * @param $entry
     *
     * @return array|false
     */
    public function getNextEntry($entry): array|false;

    /**
     * Retrieves the ldap entry's attributes.
     *
     * @link http://php.net/manual/en/function.ldap-get-attributes.php
     *
     * @param $entry
     *
     * @return array|false
     */
    public function getAttributes($entry): array|false;

    /**
     * Retrieve the last error on the current connection.
     *
     * @link http://php.net/manual/en/function.ldap-error.php
     *
     * @return string
     */
    public function getLastError(): string;

    /**
     * Return detailed information about an error.
     *
     * Returns false when there was a successful last request.
     *
     * Returns DetailedError when there was an error.
     *
     * @return DetailedError|null
     */
    public function getDetailedError(): ?DetailedError;

    /**
     * Get all binary values from the specified result entry.
     *
     * @link http://php.net/manual/en/function.ldap-get-values-len.php
     *
     * @param $entry
     * @param $attribute
     *
     * @return array
     */
    public function getValuesLen($entry, $attribute): array;

    /**
     * Sets an option on the current connection.
     *
     * @link http://php.net/manual/en/function.ldap-set-option.php
     *
     * @param int $option
     * @param mixed $value
     *
     * @return bool
     */
    public function setOption(int $option, mixed $value): bool;

    /**
     * Sets options on the current connection.
     *
     * @param array $options
     *
     * @return void
     */
    public function setOptions(array $options = []): void;

    /**
     * Set a callback function to do re-binds on referral chasing.
     *
     * @link http://php.net/manual/en/function.ldap-set-rebind-proc.php
     *
     * @param callable $callback
     *
     * @return bool
     */
    public function setRebindCallback(callable $callback): bool;

    /**
     * Connects to the specified hostname using the specified port.
     *
     * @link http://php.net/manual/en/function.ldap-start-tls.php
     *
     * @param array|string $hostname
     * @param int $port
     *
     * @return Connection|bool
     */
    public function connect(array|string $hostname = [], int $port = 389): Connection|bool;

    /**
     * Starts a connection using TLS.
     *
     * @link http://php.net/manual/en/function.ldap-start-tls.php
     *
     * @throws ConnectionException If starting TLS fails.
     *
     * @return bool
     */
    public function startTLS(): bool;

    /**
     * Binds to the current connection using the specified username and password.
     * If sasl is true, the current connection is bound using SASL.
     *
     * @link http://php.net/manual/en/function.ldap-bind.php
     *
     * @param string $username
     * @param string $password
     * @param bool $sasl
     *
     * @throws ConnectionException If starting TLS fails.
     *
     * @return bool
     */
    public function bind(string $username, string $password, bool $sasl = false): bool;

    /**
     * Closes the current connection.
     *
     * Returns false if no connection is present.
     *
     * @link http://php.net/manual/en/function.ldap-close.php
     *
     * @return bool
     */
    public function close(): bool;

    /**
     * Performs a search on the current connection.
     *
     * @link http://php.net/manual/en/function.ldap-search.php
     *
     * @param string $dn
     * @param string $filter
     * @param array $fields
     * @param bool $onlyAttributes
     * @param int $size
     * @param int $time
     *
     * @return Result|Result[]|false
     */
    // todo - add return type Result
    public function search(
        string $dn,
        string $filter,
        array $fields,
        bool $onlyAttributes = false,
        int $size = 0,
        int $time = 0
    ): mixed;

    /**
     * Reads an entry on the current connection.
     *
     * @link http://php.net/manual/en/function.ldap-read.php
     *
     * @param string $dn
     * @param string $filter
     * @param array $fields
     * @param bool $onlyAttributes
     * @param int $size
     * @param int $time
     *
     * @return Result|Result[]|false
     */
    // todo - add return type Result
    public function read(
        string $dn,
        string $filter,
        array $fields,
        bool $onlyAttributes = false,
        int $size = 0,
        int $time = 0
    ): mixed;

    /**
     * Performs a single level search on the current connection.
     *
     * @link http://php.net/manual/en/function.ldap-list.php
     *
     * @param string $dn
     * @param string $filter
     * @param array $attributes
     * @param bool $onlyAttributes
     * @param int $size
     * @param int $time
     *
     * @return Result|array|false
     */
    public function listing(
        string $dn,
        string $filter,
        array $attributes,
        bool $onlyAttributes = false,
        int $size = 0,
        int $time = 0
    ): Result|array|false;

    /**
     * Adds an entry to the current connection.
     *
     * @link http://php.net/manual/en/function.ldap-add.php
     *
     * @param string $dn
     * @param array $entry
     *
     * @return bool
     */
    public function add(string $dn, array $entry): bool;

    /**
     * Deletes an entry on the current connection.
     *
     * @link http://php.net/manual/en/function.ldap-delete.php
     *
     * @param string $dn
     *
     * @return bool
     */
    public function delete(string $dn): bool;

    /**
     * Modify the name of an entry on the current connection.
     *
     * @link http://php.net/manual/en/function.ldap-rename.php
     *
     * @param string $dn
     * @param string $newRdn
     * @param string $newParent
     * @param bool $deleteOldRdn
     *
     * @return bool
     */
    public function rename(string $dn, string $newRdn, string $newParent, bool $deleteOldRdn = false): bool;

    /**
     * Modifies an existing entry on the current connection.
     *
     * @link http://php.net/manual/en/function.ldap-modify.php
     *
     * @param string $dn
     * @param array $entry
     *
     * @return bool
     */
    public function modify(string $dn, array $entry): bool;

    /**
     * Batch modifies an existing entry on the current connection.
     *
     * @link http://php.net/manual/en/function.ldap-modify-batch.php
     *
     * @param string|null $dn
     * @param array $values
     *
     * @return bool
     */
    public function modifyBatch(?string $dn, array $values): bool;

    /**
     * Add attribute values to current attributes.
     *
     * @link http://php.net/manual/en/function.ldap-mod-add.php
     *
     * @param string $dn
     * @param array $entry
     *
     * @return bool
     */
    public function modAdd(string $dn, array $entry): bool;

    /**
     * Replaces attribute values with new ones.
     *
     * @link http://php.net/manual/en/function.ldap-mod-replace.php
     *
     * @param string $dn
     * @param array $entry
     *
     * @return bool
     */
    public function modReplace(string $dn, array $entry): bool;

    /**
     * Delete attribute values from current attributes.
     *
     * @link http://php.net/manual/en/function.ldap-mod-del.php
     *
     * @param string $dn
     * @param array $entry
     *
     * @return bool
     */
    public function modDelete(string $dn, array $entry): bool;

    /**
     * Send LDAP pagination control.
     *
     * @link http://php.net/manual/en/function.ldap-control-paged-result.php
     *
     * @param int $pageSize
     * @param bool $isCritical
     * @param string $cookie
     *
     * @return void
     */
    public function controlPagedResult(int $pageSize = 1000, bool $isCritical = false, string $cookie = ''): void;

    /**
     * Retrieve the LDAP pagination cookie.
     *
     * @link http://php.net/manual/en/function.ldap-control-paged-result-response.php
     *
     * @param $result
     * @param string $cookie
     *
     * @return void
     */
    public function controlPagedResultResponse($result, string &$cookie): void;

    /**
     * Frees up the memory allocated internally to store the result.
     *
     * @link https://www.php.net/manual/en/function.ldap-free-result.php
     *
     * @param resource $result
     *
     * @return bool
     */
    public function freeResult($result): bool;

    /**
     * Returns the error number of the last command
     * executed on the current connection.
     *
     * @link http://php.net/manual/en/function.ldap-errno.php
     *
     * @return int
     */
    public function errNo(): int;

    /**
     * Returns the extended error string of the last command.
     *
     * @return string
     */
    public function getExtendedError(): string;

    /**
     * Returns the extended error hex code of the last command.
     *
     * @return string|null
     */
    public function getExtendedErrorHex(): ?string;

    /**
     * Returns the extended error code of the last command.
     *
     * @return string
     */
    public function getExtendedErrorCode(): string;

    /**
     * Returns the error string of the specified
     * error number.
     *
     * @link http://php.net/manual/en/function.ldap-err2str.php
     *
     * @param int $number
     *
     * @return string
     */
    public function err2Str(int $number): string;

    /**
     * Return the diagnostic Message.
     *
     * @return string
     */
    public function getDiagnosticMessage(): string;

    /**
     * Extract the diagnostic code from the message.
     *
     * @param string $message
     *
     * @return string|bool
     */
    public function extractDiagnosticCode(string $message): bool|string;
}
