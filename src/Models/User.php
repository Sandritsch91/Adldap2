<?php

namespace Adldap\Models;

use Adldap\AdldapException;
use Adldap\Models\Attributes\AccountControl;
use Adldap\Models\Attributes\TSPropertyArray;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Utilities;
use DateTime;
use Illuminate\Contracts\Auth\Authenticatable;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class User.
 *
 * Represents an LDAP user.
 */
class User extends Entry implements Authenticatable
{
    use Concerns\HasUserProperties;
    use Concerns\HasDescription;
    use Concerns\HasMemberOf;
    use Concerns\HasLastLogonAndLogOff;
    use Concerns\HasUserAccountControl;

    /** @var callable|null */
    private static $passwordStrategy;

    /**
     * Password will be processed using given callback before saving.
     *
     * @param callable $strategy
     */
    public static function usePasswordStrategy(callable $strategy): void
    {
        static::$passwordStrategy = $strategy;
    }

    /**
     * Will return user set password strategy or default one.
     *
     * @return callable
     */
    public static function getPasswordStrategy(): callable
    {
        return static::$passwordStrategy ?? function ($password) {
            return Utilities::encodePassword($password);
        };
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return $this->schema->objectGuid();
    }

    /**
     * Get the unique identifier for the user.
     * @return string|null
     */
    public function getAuthIdentifier(): ?string
    {
        return $this->getConvertedGuid();
    }

    /**
     * Get the name of the password attribute for the user.
     */
    public function getAuthPasswordName(): string
    {
        return $this->schema->unicodePassword();
    }

    /**
     * Get the password for the user.
     *
     * @return void
     */
    public function getAuthPassword()
    {
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return void
     */
    public function getRememberToken()
    {
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     *
     * @return void
     */
    public function setRememberToken($value)
    {
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return void
     */
    public function getRememberTokenName()
    {
    }

    /**
     * Returns the department number.
     *
     * @return string
     */
    public function getDepartmentNumber(): string
    {
        return $this->getFirstAttribute($this->schema->departmentNumber());
    }

    /**
     * Sets the department number.
     *
     * @param string $number
     *
     * @return $this
     */
    public function setDepartmentNumber(string $number): static
    {
        return $this->setFirstAttribute($this->schema->departmentNumber(), $number);
    }

    /**
     * Returns the users info.
     *
     * @return mixed
     */
    public function getInfo(): mixed
    {
        return $this->getFirstAttribute($this->schema->info());
    }

    /**
     * Sets the users info.
     *
     * @param string $info
     *
     * @return $this
     */
    public function setInfo(string $info): static
    {
        return $this->setFirstAttribute($this->schema->info(), $info);
    }

    /**
     * Returns the users physical delivery office name.
     *
     * @return string
     */
    public function getPhysicalDeliveryOfficeName(): string
    {
        return $this->getFirstAttribute($this->schema->physicalDeliveryOfficeName());
    }

    /**
     * Sets the users physical delivery office name.
     *
     * @param string $deliveryOffice
     *
     * @return $this
     */
    public function setPhysicalDeliveryOfficeName(string $deliveryOffice): static
    {
        return $this->setFirstAttribute($this->schema->physicalDeliveryOfficeName(), $deliveryOffice);
    }

    /**
     * Returns the users locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->getFirstAttribute($this->schema->locale());
    }

    /**
     * Sets the users locale.
     *
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale(string $locale): static
    {
        return $this->setFirstAttribute($this->schema->locale(), $locale);
    }

    /**
     * Returns the users company.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675457(v=vs.85).aspx
     *
     * @return string
     */
    public function getCompany(): string
    {
        return $this->getFirstAttribute($this->schema->company());
    }

    /**
     * Sets the users company.
     *
     * @param string $company
     *
     * @return $this
     */
    public function setCompany(string $company): static
    {
        return $this->setFirstAttribute($this->schema->company(), $company);
    }

    /**
     * Returns the users mailbox store DN.
     *
     * @link https://msdn.microsoft.com/en-us/library/aa487565(v=exchg.65).aspx
     *
     * @return string
     */
    public function getHomeMdb(): string
    {
        return $this->getFirstAttribute($this->schema->homeMdb());
    }

    /**
     * Sets the users home drive.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676191(v=vs.85).aspx
     *
     * @return $this
     */
    public function setHomeDrive($drive): static
    {
        return $this->setAttribute($this->schema->homeDrive(), $drive);
    }

    /**
     * Specifies the drive letter to which to map the UNC path specified by homeDirectory.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676191(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getHomeDrive(): ?string
    {
        return $this->getFirstAttribute($this->schema->homeDrive());
    }

    /**
     * Sets the users home directory.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676190(v=vs.85).aspx
     *
     * @param string $directory
     *
     * @return $this
     */
    public function setHomeDirectory(string $directory): static
    {
        return $this->setAttribute($this->schema->homeDirectory(), $directory);
    }

    /**
     * The home directory for the account.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676190(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getHomeDirectory(): ?string
    {
        return $this->getFirstAttribute($this->schema->homeDirectory());
    }

    /**
     * The user's main home phone number.
     *
     * @link https://docs.microsoft.com/en-us/windows/desktop/ADSchema/a-homephone
     *
     * @return string|null
     */
    public function getHomePhone(): ?string
    {
        return $this->getFirstAttribute($this->schema->homePhone());
    }

    /**
     * Returns the users principal name.
     *
     * This is usually their email address.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms680857(v=vs.85).aspx
     *
     * @return string
     */
    public function getUserPrincipalName(): string
    {
        return $this->getFirstAttribute($this->schema->userPrincipalName());
    }

    /**
     * Sets the users user principal name.
     *
     * @param string $upn
     *
     * @return $this
     */
    public function setUserPrincipalName(string $upn): Model
    {
        return $this->setFirstAttribute($this->schema->userPrincipalName(), $upn);
    }

    /**
     * Returns an array of workstations the user is assigned to.
     *
     * @return array
     */
    public function getUserWorkstations(): array
    {
        $workstations = $this->getFirstAttribute($this->schema->userWorkstations());

        if ($workstations === null) {
            return [];
        }

        return array_filter(explode(',', $workstations));
    }

    /**
     * Sets the workstations the user can login to.
     *
     * @param array|string $workstations The names of the workstations the user can login to.
     *                                   Must be an array of names, or a comma separated
     *                                   list of names.
     *
     * @return $this
     */
    public function setUserWorkstations(array|string $workstations = []): static
    {
        if (is_array($workstations)) {
            $workstations = implode(',', $workstations);
        }

        return $this->setFirstAttribute($this->schema->userWorkstations(), $workstations);
    }

    /**
     * Returns the users script path if the user has one.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679656(v=vs.85).aspx
     *
     * @return string
     */
    public function getScriptPath(): string
    {
        return $this->getFirstAttribute($this->schema->scriptPath());
    }

    /**
     * Sets the users script path.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setScriptPath(string $path): static
    {
        return $this->setFirstAttribute($this->schema->scriptPath(), $path);
    }

    /**
     * Returns the users bad password count.
     *
     * @return string
     */
    public function getBadPasswordCount(): string
    {
        return $this->getFirstAttribute($this->schema->badPasswordCount());
    }

    /**
     * Returns the users bad password time.
     *
     * @return string
     */
    public function getBadPasswordTime(): string
    {
        return $this->getFirstAttribute($this->schema->badPasswordTime());
    }

    /**
     * Returns the bad password time unix timestamp.
     *
     * @return float|null
     */
    public function getBadPasswordTimestamp(): ?float
    {
        if ($time = $this->getBadPasswordTime()) {
            return Utilities::convertWindowsTimeToUnixTime($time);
        }
        return null;
    }

    /**
     * Returns the formatted timestamp of the bad password date.
     *
     * @return string|null
     * @throws \Exception
     *
     */
    public function getBadPasswordDate(): ?string
    {
        if ($timestamp = $this->getBadPasswordTimestamp()) {
            return (new DateTime())->setTimestamp($timestamp)->format($this->dateFormat);
        }
        return null;
    }

    /**
     * Returns the time when the users password was set last.
     *
     * @return string
     */
    public function getPasswordLastSet(): string
    {
        return $this->getFirstAttribute($this->schema->passwordLastSet());
    }

    /**
     * Returns the password last set unix timestamp.
     *
     * @return float|null
     */
    public function getPasswordLastSetTimestamp(): ?float
    {
        if ($time = $this->getPasswordLastSet()) {
            return Utilities::convertWindowsTimeToUnixTime($time);
        }
        return null;
    }

    /**
     * Returns the formatted timestamp of the password last set date.
     *
     * @return string|null
     * @throws \Exception
     *
     */
    public function getPasswordLastSetDate(): ?string
    {
        if ($timestamp = $this->getPasswordLastSetTimestamp()) {
            return (new DateTime())->setTimestamp($timestamp)->format($this->dateFormat);
        }
        return null;
    }

    /**
     * Returns the users lockout time.
     *
     * @return string
     */
    public function getLockoutTime(): string
    {
        return $this->getFirstAttribute($this->schema->lockoutTime());
    }

    /**
     * Returns the users lockout unix timestamp.
     *
     * @return float|null
     */
    public function getLockoutTimestamp(): ?float
    {
        if ($time = $this->getLockoutTime()) {
            return Utilities::convertWindowsTimeToUnixTime($time);
        }
        return null;
    }

    /**
     * Returns the formatted timestamp of the lockout date.
     *
     * @return string|null
     * @throws \Exception
     *
     */
    public function getLockoutDate(): ?string
    {
        if ($timestamp = $this->getLockoutTimestamp()) {
            return (new DateTime())->setTimestamp($timestamp)->format($this->dateFormat);
        }
        return null;
    }

    /**
     * Clears the accounts lockout time, unlocking the account.
     *
     * @return $this
     */
    public function setClearLockoutTime(): static
    {
        return $this->setFirstAttribute($this->schema->lockoutTime(), 0);
    }

    /**
     * Returns the users profile file path.
     *
     * @return string
     */
    public function getProfilePath(): string
    {
        return $this->getFirstAttribute($this->schema->profilePath());
    }

    /**
     * Sets the users profile path.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setProfilePath(string $path): static
    {
        return $this->setFirstAttribute($this->schema->profilePath(), $path);
    }

    /**
     * Returns the users legacy exchange distinguished name.
     *
     * @return string
     */
    public function getLegacyExchangeDn(): string
    {
        return $this->getFirstAttribute($this->schema->legacyExchangeDn());
    }

    /**
     * Sets the users account expiry date.
     *
     * If no expiry time is given, the account is set to never expire.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675098(v=vs.85).aspx
     *
     * @param float $expiryTime
     *
     * @return $this
     */
    public function setAccountExpiry(float $expiryTime): static
    {
        $time = (string)Utilities::convertUnixTimeToWindowsTime($expiryTime);

        return $this->setFirstAttribute($this->schema->accountExpires(), $time);
    }

    /**
     * Returns an array of address book DNs
     * that the user is listed to be shown in.
     *
     * @return array
     */
    public function getShowInAddressBook(): array
    {
        return $this->getAttribute($this->schema->showInAddressBook()) ?? [];
    }

    /**
     * Returns the users thumbnail photo base 64 encoded.
     *
     * Suitable for inserting into an HTML image element.
     *
     * @return string|null
     */
    public function getThumbnailEncoded(): ?string
    {
        if ($data = base64_decode($this->getThumbnail(), true)) {
            // In case we don't have the file info extension enabled,
            // we'll set the jpeg mime type as default.
            $mime = 'image/jpeg';

            $image = base64_encode($data);

            if (function_exists('finfo_open')) {
                $finfo = finfo_open();

                $mime = finfo_buffer($finfo, $data, FILEINFO_MIME_TYPE);

                return "data:$mime;base64,$image";
            }

            return "data:$mime;base64,$image";
        }
        return null;
    }

    /**
     * Returns the users thumbnail photo.
     *
     * @return mixed
     */
    public function getThumbnail(): mixed
    {
        return $this->getFirstAttribute($this->schema->thumbnail());
    }

    /**
     * Sets the users thumbnail photo.
     *
     * @param string $data
     * @param bool $encode
     *
     * @return $this
     */
    public function setThumbnail(string $data, bool $encode = true): static
    {
        if ($encode && !base64_decode($data, true)) {
            // If the string we're given is not base 64 encoded, then
            // we will encode it before setting it on the user.
            $data = base64_encode($data);
        }

        return $this->setAttribute($this->schema->thumbnail(), $data);
    }

    /**
     * Returns the users JPEG photo.
     *
     * @return null|string
     */
    public function getJpegPhotoEncoded(): ?string
    {
        $jpeg = $this->getJpegPhoto();

        return is_null($jpeg) ? $jpeg : 'data:image/jpeg;base64,' . base64_encode($jpeg);
    }

    /**
     * Returns the users JPEG photo.
     *
     * @return mixed
     */
    public function getJpegPhoto(): mixed
    {
        return $this->getFirstAttribute($this->schema->jpegPhoto());
    }

    /**
     * Sets the users JPEG photo.
     *
     * @param string $string
     *
     * @return $this
     */
    public function setJpegPhoto(string $string): static
    {
        if (!base64_decode($string, true)) {
            $string = base64_encode($string);
        }

        return $this->setAttribute($this->schema->jpegPhoto(), $string);
    }

    /**
     * Return the employee ID.
     *
     * @return string
     */
    public function getEmployeeId(): string
    {
        return $this->getFirstAttribute($this->schema->employeeId());
    }

    /**
     * Sets the employee ID.
     *
     * @param string $employeeId
     *
     * @return $this
     */
    public function setEmployeeId(string $employeeId): static
    {
        return $this->setFirstAttribute($this->schema->employeeId(), $employeeId);
    }

    /**
     * Returns the employee type.
     *
     * @return string|null
     */
    public function getEmployeeType(): ?string
    {
        return $this->getFirstAttribute($this->schema->employeeType());
    }

    /**
     * Sets the employee type.
     *
     * @param string $type
     *
     * @return $this
     */
    public function setEmployeeType(string $type): static
    {
        return $this->setFirstAttribute($this->schema->employeeType(), $type);
    }

    /**
     * Returns the employee number.
     *
     * @return string
     */
    public function getEmployeeNumber(): string
    {
        return $this->getFirstAttribute($this->schema->employeeNumber());
    }

    /**
     * Sets the employee number.
     *
     * @param string $number
     *
     * @return $this
     */
    public function setEmployeeNumber(string $number): static
    {
        return $this->setFirstAttribute($this->schema->employeeNumber(), $number);
    }

    /**
     * Returns the room number.
     *
     * @return string
     */
    public function getRoomNumber(): string
    {
        return $this->getFirstAttribute($this->schema->roomNumber());
    }

    /**
     * Sets the room number.
     *
     * @param string $number
     *
     * @return $this
     */
    public function setRoomNumber(string $number): static
    {
        return $this->setFirstAttribute($this->schema->roomNumber(), $number);
    }

    /**
     * Return the personal title.
     *
     * @return $this
     */
    public function getPersonalTitle(): static
    {
        return $this->getFirstAttribute($this->schema->personalTitle());
    }

    /**
     * Sets the personal title.
     *
     * @param string $personalTitle
     *
     * @return $this
     */
    public function setPersonalTitle(string $personalTitle): static
    {
        return $this->setFirstAttribute($this->schema->personalTitle(), $personalTitle);
    }

    /**
     * Return the user parameters.
     *
     * @return TSPropertyArray
     */
    public function getUserParameters(): TSPropertyArray
    {
        return new TSPropertyArray($this->getFirstAttribute('userparameters'));
    }

    /**
     * Sets the user parameters.
     *
     * @param TSPropertyArray $userParameters
     *
     * @return $this
     */
    public function setUserParameters(TSPropertyArray $userParameters): static
    {
        return $this->setFirstAttribute('userparameters', $userParameters->toBinary());
    }

    /**
     * Retrieves the primary group of the current user.
     *
     * @return Model|bool
     * @throws InvalidArgumentException
     */
    public function getPrimaryGroup(): Model|bool
    {
        $groupSid = preg_replace('/\d+$/', $this->getPrimaryGroupId(), $this->getConvertedSid());

        return $this->query->newInstance()->findBySid($groupSid);
    }

    /**
     * Sets the password on the current user.
     *
     * @param string $password
     *
     * @return $this
     * @throws AdldapException When no SSL or TLS secured connection is present.
     *
     */
    public function setPassword(string $password): static
    {
        $this->validateSecureConnection();

        $encodedPassword = call_user_func(static::getPasswordStrategy(), $password);

        if ($this->exists) {
            // If the record exists, we need to add a batch replace
            // modification, otherwise we'll receive a "type or
            // value" exists exception from our LDAP server.
            return $this->addModification(
                $this->newBatchModification(
                    $this->schema->unicodePassword(),
                    LDAP_MODIFY_BATCH_REPLACE,
                    [$encodedPassword]
                )
            );
        } else {
            // Otherwise, we are creating a new record
            // and we can set the attribute normally.
            return $this->setFirstAttribute(
                $this->schema->unicodePassword(),
                $encodedPassword
            );
        }
    }

    /**
     * Sets the option to force the password change at the next logon.
     *
     * Does not work if the "Password never expires" option is enabled.
     *
     * @return $this
     */
    public function setEnableForcePasswordChange(): static
    {
        return $this->setFirstAttribute($this->schema->passwordLastSet(), 0);
    }

    /**
     * Sets the option to disable forcing a password change at the next logon.
     *
     * @return $this
     */
    public function setDisableForcePasswordChange(): static
    {
        return $this->setFirstAttribute($this->schema->passwordLastSet(), -1);
    }

    /**
     * Change the password of the current user. This must be performed over SSL / TLS.
     *
     * Throws an exception on failure.
     *
     * @param string $oldPassword The new password
     * @param string $newPassword The old password
     * @param bool $replaceNotRemove Alternative password change method. Set to true if you're receiving 'CONSTRAINT'
     *                                 errors.
     *
     * @return true
     * @throws UserPasswordIncorrectException When the old password is incorrect.
     * @throws AdldapException                When an unknown cause of failure occurs.
     *
     * @throws UserPasswordPolicyException|InvalidArgumentException    When the new password does not match your password policy.
     */
    public function changePassword(string $oldPassword, string $newPassword, bool $replaceNotRemove = false): true
    {
        $this->validateSecureConnection();

        $attribute = $this->schema->unicodePassword();

        $modifications = [];

        if ($replaceNotRemove) {
            $modifications[] = $this->newBatchModification(
                $attribute,
                LDAP_MODIFY_BATCH_REPLACE,
                [call_user_func(static::getPasswordStrategy(), $newPassword)]
            );
        } else {
            // Create batch modification for removing the old password.
            $modifications[] = $this->newBatchModification(
                $attribute,
                LDAP_MODIFY_BATCH_REMOVE,
                [call_user_func(static::getPasswordStrategy(), $oldPassword)]
            );

            // Create batch modification for adding the new password.
            $modifications[] = $this->newBatchModification(
                $attribute,
                LDAP_MODIFY_BATCH_ADD,
                [call_user_func(static::getPasswordStrategy(), $newPassword)]
            );
        }

        // Add the modifications.
        foreach ($modifications as $modification) {
            $this->addModification($modification);
        }

        $result = @$this->update();

        if (!$result) {
            // If the user failed to update, we'll see if we can
            // figure out why by retrieving the extended error.
            $error = $this->query->getConnection()->getExtendedError();
            $code = $this->query->getConnection()->getExtendedErrorCode();

            throw match ($code) {
                '0000052D' => new UserPasswordPolicyException(
                    "Error: $code. Your new password does not match the password policy."
                ),
                '00000056' => new UserPasswordIncorrectException(
                    "Error: $code. Your old password is incorrect."
                ),
                default => new AdldapException($error),
            };
        }

        return $result;
    }

    /**
     * Return true / false if LDAP User is active (enabled & not expired).
     *
     * @return bool
     * @throws \Exception
     */
    public function isActive(): bool
    {
        return $this->isEnabled() && !$this->isExpired();
    }

    /**
     * Return true / false if the LDAP User is expired.
     *
     * @param DateTime|null $date Optional date
     *
     * @return bool
     * @throws \Exception
     */
    public function isExpired(?DateTime $date = null): bool
    {
        // Here we'll determine if the account expires by checking is expiration date.
        if ($expirationDate = $this->expirationDate()) {
            $date = $date ?: new DateTime();

            return $expirationDate <= $date;
        }

        // The account has no expiry date.
        return false;
    }

    /**
     * Return the expiration date of the user account.
     *
     * @return DateTime|null
     * @throws \Exception
     *
     */
    public function expirationDate(): ?DateTime
    {
        $accountExpiry = $this->getAccountExpiry();

        // If the account expiry is zero or the expiry is equal to
        // ActiveDirectory's 'never expire' value,
        // then we'll return null here.
        if ($accountExpiry == 0 || $accountExpiry == $this->getSchema()->neverExpiresDate()) {
            return null;
        }

        $unixTime = Utilities::convertWindowsTimeToUnixTime($accountExpiry);

        return (new DateTime())->setTimestamp($unixTime);
    }

    /**
     * Returns the users account expiry date.
     *
     * @return string
     */
    public function getAccountExpiry(): string
    {
        return $this->getFirstAttribute($this->schema->accountExpires());
    }

    /**
     * Returns true / false if the users password is expired.
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function passwordExpired(): bool
    {
        // First we'll check the users userAccountControl to see if
        // it contains the 'password does not expire' flag.
        if ($this->getUserAccountControlObject()->has(AccountControl::DONT_EXPIRE_PASSWORD)) {
            return false;
        }

        $lastSet = (int)$this->getPasswordLastSet();

        if ($lastSet === 0) {
            // If the users last set time is zero, the password has
            // been manually expired by an administrator.
            return true;
        }

        // We'll check if we're using the ActiveDirectory schema to retrieve
        // the max password age, as this is an AD-only feature.
        if ($this->schema instanceof ActiveDirectory) {
            $query = $this->query->newInstance();

            // We need to get the root domain object to be able to
            // retrieve the max password age on the domain.
            $rootDomainObject = $query->select($this->schema->maxPasswordAge())
                ->whereHas($this->schema->objectClass())
                ->first();

            $maxPasswordAge = $rootDomainObject->getMaxPasswordAge();

            if (empty($maxPasswordAge)) {
                // There is not a max password age set on the LDAP server.
                return false;
            }

            // convert from 100 nanosecond ticks to seconds
            $maxPasswordAgeSeconds = $maxPasswordAge / 10000000;

            $lastSetUnixEpoch = Utilities::convertWindowsTimeToUnixTime($lastSet);
            $passwordExpiryTime = $lastSetUnixEpoch - $maxPasswordAgeSeconds;

            $expiresAt = (new DateTime())->setTimestamp($passwordExpiryTime);

            // If our current time is greater than the users password
            // expiry time, the users password has expired.
            return (new DateTime())->getTimestamp() >= $expiresAt->getTimestamp();
        }

        return false;
    }
}
