<?php

namespace Adldap\Models;

/**
 * Class Printer.
 *
 * Represents an LDAP printer.
 */
class Printer extends Entry
{
    /**
     * Returns the printers name.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679385(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getPrinterName(): ?string
    {
        return $this->getFirstAttribute($this->schema->printerName());
    }

    /**
     * Returns the printers share name.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679408(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getPrinterShareName(): ?string
    {
        return $this->getFirstAttribute($this->schema->printerShareName());
    }

    /**
     * Returns the printers memory.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679396(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getMemory(): ?string
    {
        return $this->getFirstAttribute($this->schema->printerMemory());
    }

    /**
     * Returns the printers URL.
     *
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->getFirstAttribute($this->schema->url());
    }

    /**
     * Returns the printers location.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676839(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->getFirstAttribute($this->schema->location());
    }

    /**
     * Returns the server name that the
     * current printer is connected to.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679772(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getServerName(): ?string
    {
        return $this->getFirstAttribute($this->schema->serverName());
    }

    /**
     * Returns true / false if the printer can print in color.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679382(v=vs.85).aspx
     *
     * @return null|bool
     */
    public function getColorSupported(): ?bool
    {
        return $this->convertStringToBool(
            $this->getFirstAttribute(
                $this->schema->printerColorSupported()
            )
        );
    }

    /**
     * Returns true / false if the printer supports duplex printing.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679383(v=vs.85).aspx
     *
     * @return null|bool
     */
    public function getDuplexSupported(): ?bool
    {
        return $this->convertStringToBool(
            $this->getFirstAttribute(
                $this->schema->printerDuplexSupported()
            )
        );
    }

    /**
     * Returns an array of printer paper types that the printer supports.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679395(v=vs.85).aspx
     *
     * @return array
     */
    public function getMediaSupported(): array
    {
        return $this->getAttribute($this->schema->printerMediaSupported()) ?? [];
    }

    /**
     * Returns true / false if the printer supports stapling.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679410(v=vs.85).aspx
     *
     * @return null|bool
     */
    public function getStaplingSupported(): ?bool
    {
        return $this->convertStringToBool(
            $this->getFirstAttribute(
                $this->schema->printerStaplingSupported()
            )
        );
    }

    /**
     * Returns an array of the printers bin names.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679380(v=vs.85).aspx
     *
     * @return array
     */
    public function getPrintBinNames(): array
    {
        return $this->getAttribute($this->schema->printerBinNames()) ?? [];
    }

    /**
     * Returns the printers maximum resolution.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679391(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getPrintMaxResolution(): ?string
    {
        return $this->getFirstAttribute($this->schema->printerMaxResolutionSupported());
    }

    /**
     * Returns the printers orientations supported.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679402(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getPrintOrientations(): ?string
    {
        return $this->getFirstAttribute($this->schema->printerOrientationSupported());
    }

    /**
     * Returns the driver name of the printer.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675652(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getDriverName(): ?string
    {
        return $this->getFirstAttribute($this->schema->driverName());
    }

    /**
     * Returns the printer drivers version number.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675653(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getDriverVersion(): ?string
    {
        return $this->getFirstAttribute($this->schema->driverVersion());
    }

    /**
     * Returns the priority number of the printer.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679413(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getPriority(): ?string
    {
        return $this->getFirstAttribute($this->schema->priority());
    }

    /**
     * Returns the printers start time.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679411(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getPrintStartTime(): ?string
    {
        return $this->getFirstAttribute($this->schema->printerStartTime());
    }

    /**
     * Returns the printers end time.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679384(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getPrintEndTime(): ?string
    {
        return $this->getFirstAttribute($this->schema->printerEndTime());
    }

    /**
     * Returns the port name of printer.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679131(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getPortName(): ?string
    {
        return $this->getFirstAttribute($this->schema->portName());
    }

    /**
     * Returns the printers version number.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms680897(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getVersionNumber(): ?string
    {
        return $this->getFirstAttribute($this->schema->versionNumber());
    }

    /**
     * Returns the print rate.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679405(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getPrintRate(): ?string
    {
        return $this->getFirstAttribute($this->schema->printerPrintRate());
    }

    /**
     * Returns the print rate unit.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679406(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getPrintRateUnit(): ?string
    {
        return $this->getFirstAttribute($this->schema->printerPrintRateUnit());
    }
}
