<?php

namespace Adldap\Connections;

class DetailedError
{
    /**
     * The error code from ldap_errno.
     *
     * @var int|null
     */
    protected ?int $errorCode;

    /**
     * The error message from ldap_error.
     *
     * @var string|null
     */
    protected ?string $errorMessage;

    /**
     * The diagnostic message when retrieved after an ldap_error.
     *
     * @var string|null
     */
    protected ?string $diagnosticMessage;

    /**
     * Constructor.
     *
     * @param int $errorCode
     * @param string $errorMessage
     * @param string|null $diagnosticMessage
     */
    public function __construct(int $errorCode, string $errorMessage, ?string $diagnosticMessage)
    {
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->diagnosticMessage = $diagnosticMessage;
    }

    /**
     * Returns the LDAP error code.
     *
     * @return int|null
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    /**
     * Returns the LDAP error message.
     *
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Returns the LDAP diagnostic message.
     *
     * @return string|null
     */
    public function getDiagnosticMessage(): ?string
    {
        return $this->diagnosticMessage;
    }
}
