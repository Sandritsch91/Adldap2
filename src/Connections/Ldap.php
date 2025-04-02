<?php

namespace Adldap\Connections;

use LDAP\Connection;
use LDAP\Result;

/**
 * Class Ldap.
 *
 * A class that abstracts PHP's LDAP functions and stores the bound connection.
 */
class Ldap implements ConnectionInterface
{
    /**
     * The connection name.
     *
     * @var string|null
     */
    protected ?string $name;

    /**
     * The LDAP host that is currently connected.
     *
     * @var string|null
     */
    protected ?string $host = null;

    /**
     * The active LDAP connection.
     *
     * @var false|Connection|null
     */
    protected false|Connection|null $connection = null;

    /**
     * The bound status of the connection.
     *
     * @var bool
     */
    protected bool $bound = false;

    /**
     * Whether the connection must be bound over SSL.
     *
     * @var bool
     */
    protected bool $useSSL = false;

    /**
     * Whether the connection must be bound over TLS.
     *
     * @var bool
     */
    protected bool $useTLS = false;

    /**
     * {@inheritdoc}
     */
    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function isUsingSSL(): bool
    {
        return $this->useSSL;
    }

    /**
     * {@inheritdoc}
     */
    public function isUsingTLS(): bool
    {
        return $this->useTLS;
    }

    /**
     * {@inheritdoc}
     */
    public function isBound(): bool
    {
        return $this->bound;
    }

    /**
     * {@inheritdoc}
     */
    public function canChangePasswords(): bool
    {
        return $this->isUsingSSL() || $this->isUsingTLS();
    }

    /**
     * {@inheritdoc}
     */
    public function ssl(bool $enabled = true): ConnectionInterface
    {
        $this->useSSL = $enabled;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function tls(bool $enabled = true): ConnectionInterface
    {
        $this->useTLS = $enabled;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection(): Connection|false|null
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntries(Result $searchResults): array|false
    {
        return ldap_get_entries($this->connection, $searchResults);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstEntry(Result $searchResults): array|false
    {
        return ldap_first_entry($this->connection, $searchResults);
    }

    /**
     * {@inheritdoc}
     */
    public function getNextEntry($entry): array|false
    {
        return ldap_next_entry($this->connection, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($entry): array
    {
        return ldap_get_attributes($this->connection, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function countEntries(Result $searchResults): int
    {
        return ldap_count_entries($this->connection, $searchResults);
    }

    /**
     * {@inheritdoc}
     */
    public function compare(string $dn, string $attribute, string $value): int|bool
    {
        return ldap_compare($this->connection, $dn, $attribute, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastError(): string
    {
        return ldap_error($this->connection);
    }

    /**
     * {@inheritdoc}
     */
    public function getDetailedError(): ?DetailedError
    {
        // If the returned error number is zero, the last LDAP operation
        // succeeded. We won't return a detailed error.
        if ($number = $this->errNo()) {
            ldap_get_option($this->connection, LDAP_OPT_DIAGNOSTIC_MESSAGE, $message);

            return new DetailedError($number, $this->err2Str($number), $message);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getValuesLen($entry, $attribute): array
    {
        return ldap_get_values_len($this->connection, $entry, $attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function setOption(int $option, mixed $value): bool
    {
        return ldap_set_option($this->connection, $option, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options = []): void
    {
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setRebindCallback(callable $callback): bool
    {
        return ldap_set_rebind_proc($this->connection, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function startTLS(): bool
    {
        return ldap_start_tls($this->connection);
    }

    /**
     * {@inheritdoc}
     */
    public function connect(array|string $hostname = [], int $port = 389): Connection|bool
    {
        $this->host = $this->getConnectionString($hostname, $this->getProtocol(), $port);

        // Reset the bound status if reinitializing the connection.
        $this->bound = false;

        return $this->connection = ldap_connect($this->host);
    }

    /**
     * {@inheritdoc}
     */
    public function close(): bool
    {
        $connection = $this->connection;

        if ($this->connection instanceof Connection) {
            $result = ldap_close($connection);
        } else {
            $result = true;
        }

        $this->bound = false;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function search(
        string $dn,
        string $filter,
        array $fields,
        bool $onlyAttributes = false,
        int $size = 0,
        int $time = 0
    ): Result|array|false {
        return ldap_search($this->connection, $dn, $filter, $fields, $onlyAttributes, $size, $time);
    }

    /**
     * {@inheritdoc}
     */
    public function listing(
        string $dn,
        string $filter,
        array $attributes,
        bool $onlyAttributes = false,
        int $size = 0,
        int $time = 0
    ): Result|array|false {
        return ldap_list($this->connection, $dn, $filter, $attributes, $onlyAttributes, $size, $time);
    }

    /**
     * {@inheritdoc}
     */
    public function read(
        string $dn,
        string $filter,
        array $fields,
        bool $onlyAttributes = false,
        int $size = 0,
        int $time = 0
    ): Result|array|false {
        return ldap_read($this->connection, $dn, $filter, $fields, $onlyAttributes, $size, $time);
    }

    /**
     * Extract information from an LDAP result.
     *
     * @link https://www.php.net/manual/en/function.ldap-parse-result.php
     *
     * @param Result $result
     * @param int|null $errorCode
     * @param string $dn
     * @param string $errorMessage
     * @param array|null $referrals
     * @param array|null $serverControls
     *
     * @return bool
     */
    public function parseResult(
        Result $result,
        ?int &$errorCode,
        string &$dn,
        string &$errorMessage,
        ?array &$referrals,
        ?array &$serverControls = null
    ): bool {
        return ldap_parse_result($this->connection, $result, $errorCode, $dn, $errorMessage, $referrals, $serverControls);
    }

    /**
     * {@inheritdoc}
     */
    public function bind(string $username, string $password, bool $sasl = false): bool
    {
        // Prior to binding, we will upgrade our connectivity to TLS on our current
        // connection and ensure we are not already bound before upgrading.
        // This is to prevent subsequent upgrading on several binds.
        if ($this->isUsingTLS() && !$this->isBound()) {
            $this->startTLS();
        }

        if ($sasl) {
            return $this->bound = ldap_sasl_bind($this->connection, null, null, 'GSSAPI');
        }

        return $this->bound = ldap_bind(
            $this->connection,
            $username,
            html_entity_decode($password)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function add(string $dn, array $entry): bool
    {
        return ldap_add($this->connection, $dn, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $dn): bool
    {
        return ldap_delete($this->connection, $dn);
    }

    /**
     * {@inheritdoc}
     */
    public function rename(string $dn, string $newRdn, string $newParent, bool $deleteOldRdn = false): bool
    {
        return ldap_rename($this->connection, $dn, $newRdn, $newParent, $deleteOldRdn);
    }

    /**
     * {@inheritdoc}
     */
    public function modify(string $dn, array $entry): bool
    {
        return ldap_modify($this->connection, $dn, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyBatch(?string $dn, array $values): bool
    {
        return ldap_modify_batch($this->connection, $dn, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function modAdd(string $dn, array $entry): bool
    {
        return ldap_mod_add($this->connection, $dn, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function modReplace(string $dn, array $entry): bool
    {
        return ldap_mod_replace($this->connection, $dn, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function modDelete(string $dn, array $entry): bool
    {
        return ldap_mod_del($this->connection, $dn, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function controlPagedResult(int $pageSize = 1000, bool $isCritical = false, string $cookie = ''): void
    {
        throw new \BadMethodCallException('This method is not supported in this version of PHP.');
        // return ldap_control_paged_result($this->connection, $pageSize, $isCritical, $cookie);
    }

    /**
     * {@inheritdoc}
     */
    public function controlPagedResultResponse($result, string &$cookie): void
    {
        throw new \BadMethodCallException('This method is not supported in this version of PHP.');
        // return ldap_control_paged_result_response($this->connection, $result, $cookie);
    }

    /**
     * {@inheritdoc}
     */
    public function freeResult(Result $result): bool
    {
        return ldap_free_result($result);
    }

    /**
     * {@inheritdoc}
     */
    public function errNo(): int
    {
        return ldap_errno($this->connection);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedError(): string
    {
        return $this->getDiagnosticMessage();
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedErrorHex(): ?string
    {
        if (preg_match("/(?<=data\s).*?(?=,)/", $this->getExtendedError(), $code)) {
            return $code[0];
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedErrorCode(): string
    {
        return $this->extractDiagnosticCode($this->getExtendedError());
    }

    /**
     * {@inheritdoc}
     */
    public function err2Str(int $number): string
    {
        return ldap_err2str($number);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiagnosticMessage(): string
    {
        ldap_get_option($this->connection, LDAP_OPT_ERROR_STRING, $message);

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function extractDiagnosticCode(string $message): bool|string
    {
        preg_match('/^([\da-fA-F]+):/', $message, $matches);

        return $matches[1] ?? false;
    }

    /**
     * Returns the LDAP protocol to utilize for the current connection.
     *
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->isUsingSSL() ? $this::PROTOCOL_SSL : $this::PROTOCOL;
    }

    /**
     * Generates an LDAP connection string for each host given.
     *
     * @param array|string $hosts
     * @param string $protocol
     * @param int $port
     *
     * @return string
     */
    protected function getConnectionString(array|string $hosts, string $protocol, int $port): string
    {
        // If we are using SSL and using the default port, we
        // will override it to use the default SSL port.
        if ($this->isUsingSSL() && $port == 389) {
            $port = self::PORT_SSL;
        }

        // Normalize hosts into an array.
        $hosts = is_array($hosts) ? $hosts : [$hosts];

        $hosts = array_map(function ($host) use ($protocol, $port) {
            return "$protocol$host:$port";
        }, $hosts);

        return implode(' ', $hosts);
    }
}
