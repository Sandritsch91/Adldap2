<?php

namespace Adldap\Log;

use Psr\Log\LoggerInterface;

trait LogsInformation
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface|null
     */
    protected static ?LoggerInterface $logger = null;

    /**
     * Get the logger instance.
     *
     * @return LoggerInterface|null
     */
    public static function getLogger(): ?LoggerInterface
    {
        return static::$logger;
    }

    /**
     * Set the logger instance.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public static function setLogger(LoggerInterface $logger): void
    {
        static::$logger = $logger;
    }

    /**
     * Unset the logger instance.
     *
     * @return void
     */
    public static function unsetLogger(): void
    {
        static::$logger = null;
    }
}
