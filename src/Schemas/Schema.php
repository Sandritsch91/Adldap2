<?php

namespace Adldap\Schemas;

use Adldap\Models\Computer;
use Adldap\Models\Contact;
use Adldap\Models\Container;
use Adldap\Models\Entry;
use Adldap\Models\ForeignSecurityPrincipal;
use Adldap\Models\Group;
use Adldap\Models\Organization;
use Adldap\Models\OrganizationalUnit;
use Adldap\Models\Printer;
use Adldap\Models\User;

abstract class Schema implements SchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function accountExpires(): string
    {
        return 'accountexpires';
    }

    /**
     * {@inheritdoc}
     */
    public function accountName(): string
    {
        return 'samaccountname';
    }

    /**
     * {@inheritdoc}
     */
    public function accountType(): string
    {
        return 'samaccounttype';
    }

    /**
     * {@inheritdoc}
     */
    public function adminDisplayName(): string
    {
        return 'admindisplayname';
    }

    /**
     * {@inheritdoc}
     */
    public function anr(): string
    {
        return 'anr';
    }

    /**
     * {@inheritdoc}
     */
    public function badPasswordCount(): string
    {
        return 'badpwdcount';
    }

    /**
     * {@inheritdoc}
     */
    public function badPasswordTime(): string
    {
        return 'badpasswordtime';
    }

    /**
     * {@inheritdoc}
     */
    public function commonName(): string
    {
        return 'cn';
    }

    /**
     * {@inheritdoc}
     */
    public function company(): string
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function computer(): string
    {
        return 'computer';
    }

    /**
     * {@inheritdoc}
     */
    public function computerModel(): string
    {
        return Computer::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configurationNamingContext(): string
    {
        return 'configurationnamingcontext';
    }

    /**
     * {@inheritdoc}
     */
    public function contact(): string
    {
        return 'contact';
    }

    /**
     * {@inheritdoc}
     */
    public function contactModel(): string
    {
        return Contact::class;
    }

    /**
     * {@inheritdoc}
     */
    public function containerModel(): string
    {
        return Container::class;
    }

    /**
     * {@inheritdoc}
     */
    public function country(): string
    {
        return 'c';
    }

    /**
     * {@inheritdoc}
     */
    public function createdAt(): string
    {
        return 'whencreated';
    }

    /**
     * {@inheritdoc}
     */
    public function currentTime(): string
    {
        return 'currenttime';
    }

    /**
     * {@inheritdoc}
     */
    public function defaultNamingContext(): string
    {
        return 'defaultnamingcontext';
    }

    /**
     * {@inheritdoc}
     */
    public function department(): string
    {
        return 'department';
    }

    /**
     * {@inheritdoc}
     */
    public function departmentNumber(): string
    {
        return 'departmentnumber';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'description';
    }

    /**
     * {@inheritdoc}
     */
    public function displayName(): string
    {
        return 'displayname';
    }

    /**
     * {@inheritdoc}
     */
    public function dnsHostName(): string
    {
        return 'dnshostname';
    }

    /**
     * {@inheritdoc}
     */
    public function domainComponent(): string
    {
        return 'dc';
    }

    /**
     * {@inheritdoc}
     */
    public function driverName(): string
    {
        return 'drivername';
    }

    /**
     * {@inheritdoc}
     */
    public function driverVersion(): string
    {
        return 'driverversion';
    }

    /**
     * {@inheritdoc}
     */
    public function email(): string
    {
        return 'mail';
    }

    /**
     * {@inheritdoc}
     */
    public function emailNickname(): string
    {
        return 'mailnickname';
    }

    /**
     * {@inheritdoc}
     */
    public function employeeId(): string
    {
        return 'employeeid';
    }

    /**
     * {@inheritdoc}
     */
    public function employeeNumber(): string
    {
        return 'employeenumber';
    }

    /**
     * {@inheritdoc}
     */
    public function employeeType(): string
    {
        return 'employeetype';
    }

    /**
     * {@inheritdoc}
     */
    public function entryModel(): string
    {
        return Entry::class;
    }

    /**
     * {@inheritdoc}
     */
    public function false(): string
    {
        return 'FALSE';
    }

    /**
     * {@inheritdoc}
     */
    public function firstName(): string
    {
        return 'givenname';
    }

    /**
     * {@inheritdoc}
     */
    public function groupModel(): string
    {
        return Group::class;
    }

    /**
     * {@inheritdoc}
     */
    public function groupType(): string
    {
        return 'grouptype';
    }

    /**
     * {@inheritdoc}
     */
    public function homeAddress(): string
    {
        return 'homepostaladdress';
    }

    /**
     * {@inheritdoc}
     */
    public function homeMdb(): string
    {
        return 'homemdb';
    }

    /**
     * {@inheritdoc}
     */
    public function homeDrive(): ?string
    {
        return 'homedrive';
    }

    /**
     * {@inheritdoc}
     */
    public function homeDirectory(): ?string
    {
        return 'homedirectory';
    }

    /**
     * {@inheritdoc}
     */
    public function homePhone(): ?string
    {
        return 'homephone';
    }

    /**
     * {@inheritdoc}
     */
    public function info(): string
    {
        return 'info';
    }

    /**
     * {@inheritdoc}
     */
    public function initials(): string
    {
        return 'initials';
    }

    /**
     * {@inheritdoc}
     */
    public function instanceType(): string
    {
        return 'instancetype';
    }

    /**
     * {@inheritdoc}
     */
    public function ipPhone(): string
    {
        return 'ipphone';
    }

    /**
     * {@inheritdoc}
     */
    public function isCriticalSystemObject(): string
    {
        return 'iscriticalsystemobject';
    }

    /**
     * {@inheritdoc}
     */
    public function jpegPhoto(): string
    {
        return 'jpegphoto';
    }

    /**
     * {@inheritdoc}
     */
    public function lastLogOff(): string
    {
        return 'lastlogoff';
    }

    /**
     * {@inheritdoc}
     */
    public function lastLogOn(): string
    {
        return 'lastlogon';
    }

    /**
     * {@inheritdoc}
     */
    public function lastLogOnTimestamp(): string
    {
        return 'lastlogontimestamp';
    }

    /**
     * {@inheritdoc}
     */
    public function lastName(): string
    {
        return 'sn';
    }

    /**
     * {@inheritdoc}
     */
    public function legacyExchangeDn(): string
    {
        return 'legacyexchangedn';
    }

    /**
     * {@inheritdoc}
     */
    public function locale(): string
    {
        return 'l';
    }

    /**
     * {@inheritdoc}
     */
    public function location(): string
    {
        return 'location';
    }

    /**
     * {@inheritdoc}
     */
    public function manager(): string
    {
        return 'manager';
    }

    /**
     * {@inheritdoc}
     */
    public function managedBy(): string
    {
        return 'managedby';
    }

    /**
     * {@inheritdoc}
     */
    public function maxPasswordAge(): string
    {
        return 'maxpwdage';
    }

    /**
     * {@inheritdoc}
     */
    public function member(): string
    {
        return 'member';
    }

    /**
     * {@inheritdoc}
     */
    public function memberIdentifier(): string
    {
        return 'distinguishedname';
    }

    /**
     * {@inheritdoc}
     */
    public function memberOf(): string
    {
        return 'memberof';
    }

    /**
     * {@inheritdoc}
     */
    public function memberOfRecursive(): string
    {
        return 'memberof:1.2.840.113556.1.4.1941:';
    }

    /**
     * {@inheritdoc}
     */
    public function memberRange(int|string $from, int|string $to): string
    {
        return $this->member() . ";range=$from-$to";
    }

    /**
     * {@inheritdoc}
     */
    public function messageTrackingEnabled(): string
    {
        return 'messagetrackingenabled';
    }

    /**
     * {@inheritdoc}
     */
    public function msExchangeServer(): string
    {
        return 'ms-exch-exchange-server';
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'name';
    }

    /**
     * {@inheritdoc}
     */
    public function neverExpiresDate(): string
    {
        return '9223372036854775807';
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryComputer(): string
    {
        return 'computer';
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryContainer(): string
    {
        return 'container';
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryExchangePrivateMdb(): string
    {
        return 'msexchprivatemdb';
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryExchangeServer(): string
    {
        return 'msExchExchangeServer';
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryExchangeStorageGroup(): string
    {
        return 'msExchStorageGroup';
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryGroup(): string
    {
        return 'group';
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryOrganizationalUnit(): string
    {
        return 'organizational-unit';
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryPerson(): string
    {
        return 'person';
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryPrinter(): string
    {
        return 'print-queue';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClass(): string
    {
        return 'objectclass';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassComputer(): string
    {
        return 'computer';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassContact(): string
    {
        return 'contact';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassContainer(): string
    {
        return 'container';
    }

    /**
     *
     */
    public function objectClassOrganization(): string
    {
        return 'organization';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassPrinter(): string
    {
        return 'printqueue';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassUser(): string
    {
        return 'user';
    }

    /**
     *
     */
    public function objectClassForeignSecurityPrincipal(): string
    {
        return 'foreignsecurityprincipal';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassModelMap(): array
    {
        return [
            $this->objectClassComputer() => $this->computerModel(),
            $this->objectClassContact() => $this->contactModel(),
            $this->objectClassPerson() => $this->userModel(),
            $this->objectClassGroup() => $this->groupModel(),
            $this->objectClassContainer() => $this->containerModel(),
            $this->objectClassPrinter() => $this->printerModel(),
            $this->objectClassOrganization() => $this->organizationModel(),
            $this->objectClassOu() => $this->organizationalUnitModel(),
            $this->objectClassForeignSecurityPrincipal() => $this->foreignSecurityPrincipalModel(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function objectSid(): string
    {
        return 'objectsid';
    }

    /**
     * {@inheritdoc}
     */
    public function objectSidRequiresConversion(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function operatingSystem(): string
    {
        return 'operatingsystem';
    }

    /**
     * {@inheritdoc}
     */
    public function operatingSystemServicePack(): string
    {
        return 'operatingsystemservicepack';
    }

    /**
     * {@inheritdoc}
     */
    public function operatingSystemVersion(): string
    {
        return 'operatingsystemversion';
    }

    /**
     *
     */
    public function organization(): string
    {
        return 'organization';
    }

    /**
     * {@inheritdoc}
     */
    public function organizationName(): string
    {
        return 'o';
    }

    /**
     * {@inheritdoc}
     */
    public function organizationalPerson(): string
    {
        return 'organizationalperson';
    }

    /**
     * {@inheritdoc}
     */
    public function organizationalUnit(): string
    {
        return 'organizationalunit';
    }

    /**
     * {@inheritdoc}
     */
    public function organizationalUnitModel(): string
    {
        return OrganizationalUnit::class;
    }

    /**
     *
     */
    public function organizationModel(): string
    {
        return Organization::class;
    }

    /**
     * {@inheritdoc}
     */
    public function organizationalUnitShort(): string
    {
        return 'ou';
    }

    /**
     * {@inheritdoc}
     */
    public function otherMailbox(): string
    {
        return 'othermailbox';
    }

    /**
     * {@inheritdoc}
     */
    public function passwordLastSet(): string
    {
        return 'pwdlastset';
    }

    /**
     * {@inheritdoc}
     */
    public function person(): string
    {
        return 'person';
    }

    /**
     * {@inheritdoc}
     */
    public function personalTitle(): string
    {
        return 'personaltitle';
    }

    /**
     * {@inheritdoc}
     */
    public function physicalDeliveryOfficeName(): string
    {
        return 'physicaldeliveryofficename';
    }

    /**
     * {@inheritdoc}
     */
    public function portName(): string
    {
        return 'portname';
    }

    /**
     * {@inheritdoc}
     */
    public function postalCode(): string
    {
        return 'postalcode';
    }

    /**
     * {@inheritdoc}
     */
    public function postOfficeBox(): string
    {
        return 'postofficebox';
    }

    /**
     * {@inheritdoc}
     */
    public function primaryGroupId(): string
    {
        return 'primarygroupid';
    }

    /**
     * {@inheritdoc}
     */
    public function printerBinNames(): string
    {
        return 'printbinnames';
    }

    /**
     * {@inheritdoc}
     */
    public function printerColorSupported(): string
    {
        return 'printcolor';
    }

    /**
     * {@inheritdoc}
     */
    public function printerDuplexSupported(): string
    {
        return 'printduplexsupported';
    }

    /**
     * {@inheritdoc}
     */
    public function printerEndTime(): string
    {
        return 'printendtime';
    }

    /**
     * {@inheritdoc}
     */
    public function printerMaxResolutionSupported(): string
    {
        return 'printmaxresolutionsupported';
    }

    /**
     * {@inheritdoc}
     */
    public function printerMediaSupported(): string
    {
        return 'printmediasupported';
    }

    /**
     * {@inheritdoc}
     */
    public function printerMemory(): string
    {
        return 'printmemory';
    }

    /**
     * {@inheritdoc}
     */
    public function printerModel(): string
    {
        return Printer::class;
    }

    /**
     * {@inheritdoc}
     */
    public function printerName(): string
    {
        return 'printername';
    }

    /**
     * {@inheritdoc}
     */
    public function printerOrientationSupported(): string
    {
        return 'printorientationssupported';
    }

    /**
     * {@inheritdoc}
     */
    public function printerPrintRate(): string
    {
        return 'printrate';
    }

    /**
     * {@inheritdoc}
     */
    public function printerPrintRateUnit(): string
    {
        return 'printrateunit';
    }

    /**
     * {@inheritdoc}
     */
    public function printerShareName(): string
    {
        return 'printsharename';
    }

    /**
     * {@inheritdoc}
     */
    public function printerStaplingSupported(): string
    {
        return 'printstaplingsupported';
    }

    /**
     * {@inheritdoc}
     */
    public function printerStartTime(): string
    {
        return 'printstarttime';
    }

    /**
     * {@inheritdoc}
     */
    public function priority(): string
    {
        return 'priority';
    }

    /**
     * {@inheritdoc}
     */
    public function profilePath(): string
    {
        return 'profilepath';
    }

    /**
     * {@inheritdoc}
     */
    public function proxyAddresses(): string
    {
        return 'proxyaddresses';
    }

    /**
     * {@inheritdoc}
     */
    public function roomNumber(): string
    {
        return 'roomnumber';
    }

    /**
     * {@inheritdoc}
     */
    public function rootDomainNamingContext(): string
    {
        return 'rootdomainnamingcontext';
    }

    /**
     * {@inheritdoc}
     */
    public function schemaNamingContext(): string
    {
        return 'schemanamingcontext';
    }

    /**
     * {@inheritdoc}
     */
    public function scriptPath(): string
    {
        return 'scriptpath';
    }

    /**
     * {@inheritdoc}
     */
    public function serialNumber(): string
    {
        return 'serialnumber';
    }

    /**
     * {@inheritdoc}
     */
    public function serverName(): string
    {
        return 'servername';
    }

    /**
     * {@inheritdoc}
     */
    public function showInAddressBook(): string
    {
        return 'showinaddressbook';
    }

    /**
     * {@inheritdoc}
     */
    public function street(): string
    {
        return 'street';
    }

    /**
     * {@inheritdoc}
     */
    public function streetAddress(): string
    {
        return 'streetaddress';
    }

    /**
     * {@inheritdoc}
     */
    public function systemFlags(): string
    {
        return 'systemflags';
    }

    /**
     * {@inheritdoc}
     */
    public function telephone(): string
    {
        return 'telephonenumber';
    }

    /**
     * {@inheritdoc}
     */
    public function mobile(): string
    {
        return 'mobile';
    }

    /**
     * {@inheritdoc}
     */
    public function otherMobile(): string
    {
        return 'othermobile';
    }

    /**
     *
     */
    public function facsimile(): string
    {
        return 'facsimiletelephonenumber';
    }

    /**
     * {@inheritdoc}
     */
    public function thumbnail(): string
    {
        return 'thumbnailphoto';
    }

    /**
     * {@inheritdoc}
     */
    public function title(): string
    {
        return 'title';
    }

    /**
     * {@inheritdoc}
     */
    public function top(): string
    {
        return 'top';
    }

    /**
     * {@inheritdoc}
     */
    public function true(): string
    {
        return 'TRUE';
    }

    /**
     * {@inheritdoc}
     */
    public function unicodePassword(): string
    {
        return 'unicodepwd';
    }

    /**
     * {@inheritdoc}
     */
    public function updatedAt(): string
    {
        return 'whenchanged';
    }

    /**
     * {@inheritdoc}
     */
    public function url(): string
    {
        return 'url';
    }

    /**
     * {@inheritdoc}
     */
    public function user(): string
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function userAccountControl(): string
    {
        return 'useraccountcontrol';
    }

    /**
     * {@inheritdoc}
     */
    public function userId(): string
    {
        return 'uid';
    }

    /**
     * {@inheritdoc}
     */
    public function userModel(): string
    {
        return User::class;
    }

    /**
     * {@inheritdoc}
     */
    public function userObjectClasses(): array
    {
        return [
            $this->top(),
            $this->person(),
            $this->organizationalPerson(),
            $this->objectClassUser(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function userPrincipalName(): string
    {
        return 'userprincipalname';
    }

    /**
     * {@inheritdoc}
     */
    public function userWorkstations(): string
    {
        return 'userworkstations';
    }

    /**
     * {@inheritdoc}
     */
    public function versionNumber(): string
    {
        return 'versionnumber';
    }

    /**
     *
     */
    public function foreignSecurityPrincipalModel(): string
    {
        return ForeignSecurityPrincipal::class;
    }
}
