<?php

namespace Adldap\Schemas;

interface SchemaInterface
{
    /**
     * The date when the account expires. This value represents the number of 100-nanosecond
     * intervals since January 1, 1601 (UTC). A value of 0 or 0x7FFFFFFFFFFFFFFF
     * (9223372036854775807) indicates that the account never expires.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675098(v=vs.85).aspx
     *
     * @return string
     */
    public function accountExpires(): string;

    /**
     * The logon name used to support clients and servers running earlier versions of the
     * operating system, such as Windows NT 4.0, Windows 95, Windows 98,
     * and LAN Manager. This attribute must be 20 characters or
     * less to support earlier clients.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679635(v=vs.85).aspx
     *
     * @return string
     */
    public function accountName(): string;

    /**
     * This attribute contains information about every account type object.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679637(v=vs.85).aspx
     *
     * @return string
     */
    public function accountType(): string;

    /**
     * The name to be displayed on admin screens.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675214(v=vs.85).aspx
     *
     * @return string
     */
    public function adminDisplayName(): string;

    /**
     * Ambiguous name resolution attribute to be used when choosing between objects.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675223(v=vs.85).aspx
     *
     * @return string
     */
    public function anr(): string;

    /**
     * The number of times the user tried to log on to the account using
     * an incorrect password. A value of 0 indicates that the
     * value is unknown.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675244(v=vs.85).aspx
     *
     * @return string
     */
    public function badPasswordCount(): string;

    /**
     * The last time and date that an attempt to log on to this
     * account was made with a password that is not valid.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675243(v=vs.85).aspx
     *
     * @return string
     */
    public function badPasswordTime(): string;

    /**
     * The name that represents an object.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675449(v=vs.85).aspx
     *
     * @return string
     */
    public function commonName(): string;

    /**
     * The user's company name.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675457(v=vs.85).aspx
     *
     * @return string
     */
    public function company(): string;

    /**
     * The object class computer string.
     *
     * Used when constructing new Computer models.
     *
     * @return string
     */
    public function computer(): string;

    /**
     * The class name of the Computer model.
     *
     * @return string
     */
    public function computerModel(): string;

    /**
     * DN enterprise configuration naming context.
     *
     * @link https://support.microsoft.com/en-us/kb/219005
     *
     * @return string
     */
    public function configurationNamingContext(): string;

    /**
     * The object class contact string.
     *
     * Used when constructing new User models.
     *
     * @return string
     */
    public function contact(): string;

    /**
     * The class name of the Contact model.
     *
     * @return string
     */
    public function contactModel(): string;

    /**
     * The class name of the Container model.
     *
     * @return string
     */
    public function containerModel(): string;

    /**
     * The entry's country attribute.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675432(v=vs.85).aspx
     *
     * @return string
     */
    public function country(): string;

    /**
     * The entry's created at attribute.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms680924(v=vs.85).aspx
     *
     * @return string
     */
    public function createdAt(): string;

    /**
     * The entry's current time attribute.
     *
     * This attribute is only available with the Root DSE record.
     *
     * @return string
     */
    public function currentTime(): string;

    /**
     * This is the default NC for a particular server.
     *
     * By default, the DN for the domain of which this directory server is a member.
     *
     * @link https://support.microsoft.com/en-us/kb/219005
     *
     * @return string
     */
    public function defaultNamingContext(): string;

    /**
     * Contains the name for the department in which the user works.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675490(v=vs.85).aspx
     *
     * @return string
     */
    public function department(): string;

    /**
     * Identifies a department within an organization.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675491(v=vs.85).aspx
     *
     * @return string
     */
    public function departmentNumber(): string;

    /**
     * Contains the description to display for an object. This value is restricted
     * as single-valued for backward compatibility in some cases but
     * is allowed to be multi-valued in others.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675492(v=vs.85).aspx
     *
     * @return string
     */
    public function description(): string;

    /**
     * The display name for an object. This is usually the combination
     * of the users first name, middle initial, and last name.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675514(v=vs.85).aspx
     *
     * @return string
     */
    public function displayName(): string;

    /**
     * The LDAP API references an LDAP object by its distinguished name (DN).
     *
     * A DN is a sequence of relative distinguished names (RDN) connected by commas.
     *
     * @link https://msdn.microsoft.com/en-us/library/aa366101(v=vs.85).aspx
     *
     * @return string
     */
    public function distinguishedName(): string;

    /**
     * The LDAP API references an LDAP object by its distinguished name (DN).
     *
     * Different vendors expect the value of the distinguished name to be in
     * different places. For example ActiveDirectory expects distinguishedname
     * value to be the first element in an array, however OpenLDAP expects
     * the dn attribute to contain the value, not an array.
     *
     * @return int|null
     * @deprecated since 10.0.0
     *
     */
    public function distinguishedNameSubKey(): ?int;

    /**
     * Name of computer as registered in DNS.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675524(v=vs.85).aspx
     *
     * @return string
     */
    public function dnsHostName(): string;

    /**
     * Domain Component located inside an RDN.
     *
     * @link https://msdn.microsoft.com/en-us/library/aa366101(v=vs.85).aspx
     *
     * @return string
     */
    public function domainComponent(): string;

    /**
     * The device driver name.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675652(v=vs.85).aspx
     *
     * @return string
     */
    public function driverName(): string;

    /**
     * The Version number of device driver.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675653(v=vs.85).aspx
     *
     * @return string
     */
    public function driverVersion(): string;

    /**
     * The list of email addresses for a contact.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676855(v=vs.85).aspx
     *
     * @return string
     */
    public function email(): string;

    /**
     * The email nickname for the user.
     *
     * @return string
     */
    public function emailNickname(): string;

    /**
     * The ID of an employee.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675662(v=vs.85).aspx
     *
     * @return string
     */
    public function employeeId(): string;

    /**
     * The number assigned to an employee other than the ID.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675663(v=vs.85).aspx
     *
     * @return string
     */
    public function employeeNumber(): string;

    /**
     * The job category for an employee.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675664(v=vs.85).aspx
     *
     * @return string
     */
    public function employeeType(): string;

    /**
     * The class name of the Entry model.
     *
     * @return string
     */
    public function entryModel(): string;

    /**
     * The LDAP `false` boolean in string form for conversion.
     *
     * @return string
     */
    public function false(): string;

    /**
     * The LDAP filter to query for enabled users.
     *
     * @return mixed
     */
    public function filterEnabled(): mixed;

    /**
     * The LDAP filter to query for disabled users.
     *
     * @return mixed
     */
    public function filterDisabled(): mixed;

    /**
     * Contains the given name (first name) of the user.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675719(v=vs.85).aspx
     *
     * @return string
     */
    public function firstName(): string;

    /**
     * The class name of the Group model.
     *
     * @return string
     */
    public function groupModel(): string;

    /**
     * Contains a set of flags that define the type and scope of a group object.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675935(v=vs.85).aspx
     *
     * @return string
     */
    public function groupType(): string;

    /**
     * A user's home address.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676193(v=vs.85).aspx
     *
     * @return string
     */
    public function homeAddress(): string;

    /**
     * The users mailbox database location.
     *
     * @return string
     */
    public function homeMdb(): string;

    /**
     * Specifies the drive letter to which to map the UNC path specified by homeDirectory.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676191(v=vs.85).aspx
     *
     * @return string|null
     */
    public function homeDrive(): ?string;

    /**
     * The home directory for the account.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676190(v=vs.85).aspx
     *
     * @return string|null
     */
    public function homeDirectory(): ?string;

    /**
     * The user's main home phone number.
     *
     * @link https://docs.microsoft.com/en-us/windows/desktop/ADSchema/a-homephone
     *
     * @return string|null
     */
    public function homePhone(): ?string;

    /**
     * The users extra notable information.
     *
     * @return string
     */
    public function info(): string;

    /**
     * Contains the initials for parts of the user's full name.
     *
     * This may be used as the middle initial in the Windows Address Book.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676202(v=vs.85).aspx
     *
     * @return string
     */
    public function initials(): string;

    /**
     * A bitfield that dictates how the object is instantiated on a particular server.
     *
     * The value of this attribute can differ on different replicas even if the replicas are in sync.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676204(v=vs.85).aspx
     *
     * @return string
     */
    public function instanceType(): string;

    /**
     * Specifies the TCP/IP address for the phone. Used by telephony.
     *
     * @link https://msdn.microsoft.com/en-us/library/cc221092.aspx
     *
     * @return string
     */
    public function ipPhone(): string;

    /**
     * If TRUE, the object hosting this attribute must be replicated during installation of a new replica.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676798(v=vs.85).aspx
     *
     * @return string
     */
    public function isCriticalSystemObject(): string;

    /**
     * Used to store one or more images of a person using the JPEG File Interchange Format [JFIF].
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676813(v=vs.85).aspx
     *
     * @return string
     */
    public function jpegPhoto(): string;

    /**
     * This attribute is not used.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676822(v=vs.85).aspx
     *
     * @return string
     */
    public function lastLogOff(): string;

    /**
     * The last time the user logged on. This value is stored as a large integer that
     * represents the number of 100-nanosecond intervals since January 1, 1601 (UTC).
     *
     * A value of zero means that the last logon time is unknown.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676823(v=vs.85).aspx
     *
     * @return string
     */
    public function lastLogOn(): string;

    /**
     * This is the time that the user last logged into the domain.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676824(v=vs.85).aspx
     *
     * @return string
     */
    public function lastLogOnTimestamp(): string;

    /**
     * This attribute contains the family or last name for a user.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679872(v=vs.85).aspx
     *
     * @return string
     */
    public function lastName(): string;

    /**
     * The distinguished name previously used by Exchange.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676830(v=vs.85).aspx
     *
     * @return string
     */
    public function legacyExchangeDn(): string;

    /**
     * The users locale.
     *
     * @return string
     */
    public function locale(): string;

    /**
     * The user's location, such as office number.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676839(v=vs.85).aspx
     *
     * @return string
     */
    public function location(): string;

    /**
     * The date and time (UTC) that this account was locked out. This value is stored
     * as a large integer that represents the number of 100-nanosecond intervals
     * since January 1, 1601 (UTC). A value of zero means that the
     * account is not currently locked out.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676843(v=vs.85).aspx
     *
     * @return string
     */
    public function lockoutTime(): string;

    /**
     * Contains the distinguished name of the user who is the user's manager.
     *
     * The manager's user object contains a directReports property that
     * contains references to all user objects that have their manager
     * properties set to this distinguished name.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676859(v=vs.85).aspx
     *
     * @return string
     */
    public function manager(): string;

    /**
     * The distinguished name of the user that is assigned to manage this object.
     *
     * @link https://docs.microsoft.com/en-us/windows/desktop/adschema/a-managedby
     *
     * @return string
     */
    public function managedBy(): string;

    /**
     * The maximum amount of time, in 100-nanosecond intervals, a password is valid.
     *
     * This value is stored as a large integer that represents the number of
     * 100-nanosecond intervals from the time the password was set
     * before the password expires.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676863(v=vs.85).aspx
     *
     * @return string
     */
    public function maxPasswordAge(): string;

    /**
     * The list of users that belong to the group.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms677097(v=vs.85).aspx
     *
     * @return string
     */
    public function member(): string;

    /**
     * The identifier of records that belong to a group.
     *
     * For example, in ActiveDirectory, the 'member' attribute on
     * a group record contains a list of distinguished names,
     * so `distinguishedname` would be the identifier.
     *
     * In other environments such as Sun Directory
     * Server, this identifier would be `uid`.
     *
     * @return string
     */
    public function memberIdentifier(): string;

    /**
     * The distinguished names of the groups to which this object belongs.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms677099(v=vs.85).aspx
     *
     * @return string
     */
    public function memberOf(): string;

    /**
     * The distinguished names of the groups to which this object belongs.
     *
     * This string contains a rule OID indicating the inclusion of ancestral and child members.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms677099(v=vs.85).aspx
     * @link https://msdn.microsoft.com/en-us/library/aa746475(v=vs.85).aspx
     *
     * @return string
     */
    public function memberOfRecursive(): string;

    /**
     * The range limited list of users that belong to the group. See range limit in Active Directory
     * (Range Retrieval of Attribute Values https://msdn.microsoft.com/en-us/library/cc223242.aspx)
     * Issue #342.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms677097(v=vs.85).aspx
     *
     * @param int|string $from
     * @param int|string $to
     *
     * @return string
     */
    public function memberRange(int|string $from, int|string $to): string;

    /**
     * @link https://msdn.microsoft.com/en-us/library/ms981934(v=exchg.65).aspx
     *
     * @return string
     */
    public function messageTrackingEnabled(): string;

    /**
     * The object category of an exchange server.
     *
     * @return string
     */
    public function msExchangeServer(): string;

    /**
     * The general name of the entry.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Returns a string value indicating that an account does not expire.
     *
     * @return string
     */
    public function neverExpiresDate(): string;

    /**
     * An object class name used to group objects of this or derived classes.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679011(v=vs.85).aspx
     *
     * @return string
     */
    public function objectCategory(): string;

    /**
     * The computer object category.
     *
     * @return string
     */
    public function objectCategoryComputer(): string;

    /**
     * The container object category.
     *
     * @return string
     */
    public function objectCategoryContainer(): string;

    /**
     * The exchange private MDB category.
     *
     * @return string
     */
    public function objectCategoryExchangePrivateMdb(): string;

    /**
     * The exchange server object category.
     *
     * @return string
     */
    public function objectCategoryExchangeServer(): string;

    /**
     * The exchange storage group object category.
     *
     * @return string
     */
    public function objectCategoryExchangeStorageGroup(): string;

    /**
     * The group object category.
     *
     * @return string
     */
    public function objectCategoryGroup(): string;

    /**
     * The organizational unit category.
     *
     * @return string
     */
    public function objectCategoryOrganizationalUnit(): string;

    /**
     * The person object category.
     *
     * @return string
     */
    public function objectCategoryPerson(): string;

    /**
     * The printer object category.
     *
     * @return string
     */
    public function objectCategoryPrinter(): string;

    /**
     * The list of classes from which this class is derived.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679012(v=vs.85).aspx
     *
     * @return string
     */
    public function objectClass(): string;

    /**
     * The computer object class.
     *
     * @return string
     */
    public function objectClassComputer(): string;

    /**
     * The contact object class.
     *
     * @return string
     */
    public function objectClassContact(): string;

    /**
     * The container object class.
     *
     * @return string
     */
    public function objectClassContainer(): string;

    /**
     * The group object class.
     *
     * @return string
     */
    public function objectClassGroup(): string;

    /**
     * The ou object class.
     *
     * @return string
     */
    public function objectClassOu(): string;

    /**
     * The person object class.
     *
     * Represents people who are associated with an organization in some way.
     *
     * @return string
     */
    public function objectClassPerson(): string;

    /**
     * The printer object class.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms683911(v=vs.85).aspx
     *
     * @return string
     */
    public function objectClassPrinter(): string;

    /**
     * The user object class.
     *
     * @return string
     */
    public function objectClassUser(): string;

    /**
     * The object class model map.
     *
     * @return array
     */
    public function objectClassModelMap(): array;

    /**
     * The unique identifier for an object.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679021(v=vs.85).aspx
     *
     * @return string
     */
    public function objectGuid(): string;

    /**
     * Determine whether the object GUID requires conversion from binary.
     *
     * @return bool
     */
    public function objectGuidRequiresConversion(): bool;

    /**
     * A binary value that specifies the security identifier (SID) of the user.
     *
     * The SID is a unique value used to identify the user as a security principal.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679024(v=vs.85).aspx
     *
     * @return string
     */
    public function objectSid(): string;

    /**
     * Determine whether the object SID requires conversion from binary.
     *
     * @return bool
     */
    public function objectSidRequiresConversion(): bool;

    /**
     * The Operating System name, for example, Windows Vista Enterprise.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679076(v=vs.85).aspx
     *
     * @return string
     */
    public function operatingSystem(): string;

    /**
     * The operating system service pack ID string (for example, SP3).
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679078(v=vs.85).aspx
     *
     * @return string
     */
    public function operatingSystemServicePack(): string;

    /**
     * The operating system version string, for example, 4.0.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679079(v=vs.85).aspx
     *
     * @return string
     */
    public function operatingSystemVersion(): string;

    /**
     * The RDN version of organization name for use in distinguished names.
     *
     * @return mixed
     */
    public function organizationName(): mixed;

    /**
     * This class is used for objects that contain organizational information about a user,
     * such as the employee number, department, manager, title, office address, and so on.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms683883(v=vs.85).aspx
     *
     * @return string
     */
    public function organizationalPerson(): string;

    /**
     * A container for storing users, computers, and other account objects.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms683886(v=vs.85).aspx
     *
     * @return string
     */
    public function organizationalUnit(): string;

    /**
     * The class name of the Organizational Unit model.
     *
     * @return string
     */
    public function organizationalUnitModel(): string;

    /**
     * The RDN version of organizational unit for use in distinguished names.
     *
     * @return string
     */
    public function organizationalUnitShort(): string;

    /**
     * Contains other additional mail addresses in a form such as CCMAIL: JohnDoe.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679091(v=vs.85).aspx
     *
     * @return string
     */
    public function otherMailbox(): string;

    /**
     * The date and time that the password for this account was last changed.
     *
     * This value is stored as a large integer that represents the number of 100 nanosecond intervals
     * since January 1, 1601 (UTC). If this value is set to 0 and the User-Account-Control attribute
     * does not contain the UF_DONT_EXPIRE_PASSWD flag, then the user must set the password at
     * the next logon.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679430(v=vs.85).aspx
     *
     * @return string
     */
    public function passwordLastSet(): string;

    /**
     * The person object class.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms683895(v=vs.85).aspx
     *
     * @return string
     */
    public function person(): string;

    /**
     * The user's title.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679115(v=vs.85).aspx
     *
     * @return string
     */
    public function personalTitle(): string;

    /**
     * Contains the office location in the user's place of business.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679117(v=vs.85).aspx
     *
     * @return string
     */
    public function physicalDeliveryOfficeName(): string;

    /**
     * List of port names. For example, for printer ports or comm ports.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679131(v=vs.85).aspx
     *
     * @return string
     */
    public function portName(): string;

    /**
     * The postal or zip code for mail delivery.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679366(v=vs.85).aspx
     *
     * @return string
     */
    public function postalCode(): string;

    /**
     * The post office box number for this object.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679367(v=vs.85).aspx
     *
     * @return string
     */
    public function postOfficeBox(): string;

    /**
     * Contains the relative identifier (RID) for the primary group of the user.
     *
     * By default, this is the RID for the Domain Users group.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679375(v=vs.85).aspx
     *
     * @return string
     */
    public function primaryGroupId(): string;

    /**
     * A list of printer bin names.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679380(v=vs.85).aspx
     *
     * @return string
     */
    public function printerBinNames(): string;

    /**
     * If a printer can print in color.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679382(v=vs.85).aspx
     *
     * @return string
     */
    public function printerColorSupported(): string;

    /**
     * Indicates the type of duplex support a printer has.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679383(v=vs.85).aspx
     *
     * @return string
     */
    public function printerDuplexSupported(): string;

    /**
     * The time a print queue stops servicing jobs.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679384(v=vs.85).aspx
     *
     * @return string
     */
    public function printerEndTime(): string;

    /**
     * The maximum printer resolution.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679391(v=vs.85).aspx
     *
     * @return string
     */
    public function printerMaxResolutionSupported(): string;

    /**
     * A list of media supported by a printer.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679395(v=vs.85).aspx
     *
     * @return string
     */
    public function printerMediaSupported(): string;

    /**
     * The amount of memory installed in a printer.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679396(v=vs.85).aspx
     *
     * @return string
     */
    public function printerMemory(): string;

    /**
     * The class name of the Printer model.
     *
     * @return string
     */
    public function printerModel(): string;

    /**
     * The display name of an attached printer.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679385(v=vs.85).aspx
     *
     * @return string
     */
    public function printerName(): string;

    /**
     * The page rotation for landscape printing.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679402(v=vs.85).aspx
     *
     * @return string
     */
    public function printerOrientationSupported(): string;

    /**
     * Driver-supplied print rate.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679405(v=vs.85).aspx
     *
     * @return string
     */
    public function printerPrintRate(): string;

    /**
     * Driver-supplied print rate unit.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679406(v=vs.85).aspx
     *
     * @return string
     */
    public function printerPrintRateUnit(): string;

    /**
     * The printer's share name.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679408(v=vs.85).aspx
     *
     * @return string
     */
    public function printerShareName(): string;

    /**
     * If the printer supports stapling. Supplied by the driver.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679410(v=vs.85).aspx
     *
     * @return string
     */
    public function printerStaplingSupported(): string;

    /**
     * The time a print queue begins servicing jobs.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679411(v=vs.85).aspx
     *
     * @return string
     */
    public function printerStartTime(): string;

    /**
     * The current priority (of a process, print job, and so on).
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679413(v=vs.85).aspx
     *
     * @return string
     */
    public function priority(): string;

    /**
     * Specifies a path to the user's profile. This value can be a null
     * string, a local absolute path, or a UNC path.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679422(v=vs.85).aspx
     *
     * @return string
     */
    public function profilePath(): string;

    /**
     * A proxy address is the address by which a Microsoft Exchange Server recipient
     * object is recognized in a foreign mail system. Proxy addresses are required
     * for all recipient objects, such as custom recipients and distribution lists.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679424(v=vs.85).aspx
     *
     * @return string
     */
    public function proxyAddresses(): string;

    /**
     * The room number of an object.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679615(v=vs.85).aspx
     *
     * @return string
     */
    public function roomNumber(): string;

    /**
     * The DN of the root domain NC for this DC's forest.
     *
     * @link https://msdn.microsoft.com/en-us/library/cc223262.aspx
     *
     * @return mixed
     */
    public function rootDomainNamingContext(): mixed;

    /**
     * The attribute.
     *
     * @return mixed
     */
    public function schemaNamingContext(): mixed;

    /**
     * This attribute specifies the path for the user's logon script. The string can be null.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679656(v=vs.85).aspx
     *
     * @return string
     */
    public function scriptPath(): string;

    /**
     * Part of X.500 specification. Not used by Active Directory.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679771(v=vs.85).aspx
     *
     * @return string
     */
    public function serialNumber(): string;

    /**
     * The name of a server.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679772(v=vs.85).aspx
     *
     * @return string
     */
    public function serverName(): string;

    /**
     * This attribute is used to indicate in which MAPI address books an object will appear.
     *
     * It is usually maintained by the Exchange Recipient Update Service.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679822(v=vs.85).aspx
     *
     * @return string
     */
    public function showInAddressBook(): string;

    /**
     * The street address.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679882(v=vs.85).aspx
     *
     * @return string
     */
    public function street(): string;

    /**
     * The street address.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679882(v=vs.85).aspx
     *
     * @return string
     */
    public function streetAddress(): string;

    /**
     * An integer value that contains flags that define additional properties of the class.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms680022(v=vs.85).aspx
     *
     * @return string
     */
    public function systemFlags(): string;

    /**
     * The primary telephone number.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms680027(v=vs.85).aspx
     *
     * @return string
     */
    public function telephone(): string;

    /**
     * The primary mobile phone number.
     *
     * @link https://docs.microsoft.com/en-us/windows/desktop/adschema/a-mobile
     *
     * @return string
     */
    public function mobile(): string;

    /**
     * The secondary mobile phone number.
     *
     * @link https://docs.microsoft.com/en-us/windows/desktop/ADSchema/a-othermobile
     *
     * @return string
     */
    public function otherMobile(): string;

    /**
     * The users thumbnail photo path.
     *
     * @return string
     */
    public function thumbnail(): string;

    /**
     * Contains the user's job title.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms680037(v=vs.85).aspx
     *
     * @return string
     */
    public function title(): string;

    /**
     * The top level class from which all classes are derived.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms683975(v=vs.85).aspx
     *
     * @return string
     */
    public function top(): string;

    /**
     * The LDAP `true` boolean in string form for conversion.
     *
     * @return string
     */
    public function true(): string;

    /**
     * The password of the user in Windows NT one-way format (OWF). Windows 2000 uses the Windows NT OWF.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms680513(v=vs.85).aspx
     *
     * @return string
     */
    public function unicodePassword(): string;

    /**
     * The date when this object was last changed.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms680921(v=vs.85).aspx
     *
     * @return string
     */
    public function updatedAt(): string;

    /**
     * The entry's URL attribute.
     *
     * @return string
     */
    public function url(): string;

    /**
     * The user object class.
     *
     * This class is used to store information about an employee or contractor who works for an organization.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms683980(v=vs.85).aspx
     *
     * @return string
     */
    public function user(): string;

    /**
     * Flags that control the behavior of the user account.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms680832(v=vs.85).aspx
     *
     * @return string
     */
    public function userAccountControl(): string;

    /**
     * The user ID attribute.
     *
     * @return string
     */
    public function userId(): string;

    /**
     * The class name of the User model.
     *
     * @return string
     */
    public function userModel(): string;

    /**
     * The object classes that User models must be constructed with.
     *
     * @return array
     */
    public function userObjectClasses(): array;

    /**
     * This attribute contains the UPN that is an Internet-style login name for
     * a user based on the Internet standard RFC 822.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms680857(v=vs.85).aspx
     *
     * @return string
     */
    public function userPrincipalName(): string;

    /**
     * Contains the NetBIOS or DNS names of the computers running Windows NT Workstation
     * or Windows 2000 Professional from which the user can log on.
     *
     * Each NetBIOS name is separated by a comma.
     *
     * Multiple names should be separated by commas.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms680868(v=vs.85).aspx
     *
     * @return string
     */
    public function userWorkstations(): string;

    /**
     * A general purpose version number.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms680897(v=vs.85).aspx
     *
     * @return string
     */
    public function versionNumber(): string;
}
