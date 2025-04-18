<?php

namespace Adldap\Tests\Auth;

use Adldap\Auth\Guard;
use Adldap\Tests\TestCase;
use Adldap\Connections\Ldap;
use Adldap\Auth\Events\Bound;
use Adldap\Events\Dispatcher;
use Adldap\Auth\BindException;
use Adldap\Auth\Events\Passed;
use Adldap\Auth\Events\Binding;
use Adldap\Auth\Events\Attempting;
use Adldap\Connections\DetailedError;
use Adldap\Auth\PasswordRequiredException;
use Adldap\Auth\UsernameRequiredException;
use Adldap\Configuration\DomainConfiguration;

class GuardTest extends TestCase
{
    public function test_validate_username()
    {
        $this->expectException(UsernameRequiredException::class);

        $guard = new Guard(new Ldap(), new DomainConfiguration());

        $guard->attempt('', 'password');
    }

    public function test_validate_password()
    {
        $this->expectException(PasswordRequiredException::class);

        $guard = new Guard(new Ldap(), new DomainConfiguration());

        $guard->attempt('username', '');
    }

    public function test_attempt()
    {
        $config = $this->mock(DomainConfiguration::class);

        $config
            ->shouldReceive('get')->withArgs(['account_prefix'])->once()
            ->shouldReceive('get')->withArgs(['account_suffix'])->once()
            ->shouldReceive('get')->withArgs(['username'])->once()
            ->shouldReceive('get')->withArgs(['password'])->once();

        $ldap = $this->mock(Ldap::class);

        $ldap->shouldReceive('bind')->twice()->andReturn(true);

        $guard = new Guard($ldap, $config);

        $this->assertTrue($guard->attempt('username', 'password'));
    }

    public function test_bind_using_credentials()
    {
        $config = $this->mock(DomainConfiguration::class);

        $ldap = $this->mock(Ldap::class);

        $ldap->shouldReceive('bind')->once()->withArgs(['username', 'password'])->andReturn(true);

        $guard = new Guard($ldap, $config);

        $this->assertNull($guard->bind('username', 'password'));
    }

    public function test_bind_always_throws_exception_on_invalid_credentials()
    {
        $this->expectException(BindException::class);

        $config = $this->mock(DomainConfiguration::class);

        $ldap = $this->mock(Ldap::class);

        $ldap
            ->shouldReceive('bind')->once()->withArgs(['username', 'password'])->andReturn(false)
            ->shouldReceive('getLastError')->once()->andReturn('error')
            ->shouldReceive('getDetailedError')->once()->andReturn(new DetailedError(42, 'Invalid credentials', '80090308: LdapErr: DSID-0C09042A'))
            ->shouldReceive('errNo')->once()->andReturn(1);

        $guard = new Guard($ldap, $config);

        $guard->bind('username', 'password');
    }

    public function test_bind_as_administrator()
    {
        $config = $this->mock(DomainConfiguration::class);

        $config
            ->shouldReceive('get')->withArgs(['username'])->once()->andReturn('admin')
            ->shouldReceive('get')->withArgs(['password'])->once()->andReturn('password');

        $ldap = $this->mock(Ldap::class);

        $ldap->shouldReceive('bind')->once()->withArgs(['admin', 'password'])->andReturn(true);

        $guard = new Guard($ldap, $config);

        $this->assertNull($guard->bindAsAdministrator());
    }

    public function test_prefix_and_suffix_are_being_used_in_attempt()
    {
        $config = $this->mock(DomainConfiguration::class);

        $config
            ->shouldReceive('get')->withArgs(['account_prefix'])->once()->andReturn('prefix.')
            ->shouldReceive('get')->withArgs(['account_suffix'])->once()->andReturn('.suffix');

        $ldap = $this->mock(Ldap::class);

        $ldap->shouldReceive('bind')->once()->withArgs(['prefix.username.suffix', 'password'])->andReturn(true);

        $guard = new Guard($ldap, $config);

        $this->assertTrue($guard->attempt('username', 'password', $bindAsUser = true));
    }

    public function test_binding_events_are_fired_during_bind()
    {
        $ldap = $this->mock(Ldap::class);

        $ldap->shouldReceive('bind')->once()->withArgs(['johndoe', 'secret'])->andReturn(true);

        $events = new Dispatcher();

        $firedBinding = false;
        $firedBound = false;

        $events->listen(Binding::class, function (Binding $event) use (&$firedBinding) {
            $this->assertEquals('johndoe', $event->getUsername());
            $this->assertEquals('secret', $event->getPassword());

            $firedBinding = true;
        });

        $events->listen(Bound::class, function (Bound $event) use (&$firedBound) {
            $this->assertEquals('johndoe', $event->getUsername());
            $this->assertEquals('secret', $event->getPassword());

            $firedBound = true;
        });

        $guard = new Guard($ldap, new DomainConfiguration([]));

        $guard->setDispatcher($events);

        $guard->bind('johndoe', 'secret');

        $this->assertTrue($firedBinding);
        $this->assertTrue($firedBound);
    }

    public function test_auth_events_are_fired_during_attempt()
    {
        $config = $this->mock(DomainConfiguration::class);

        $config
            ->shouldReceive('get')->withArgs(['account_prefix'])->once()->andReturn('prefix.')
            ->shouldReceive('get')->withArgs(['account_suffix'])->once()->andReturn('.suffix');

        $ldap = $this->mock(Ldap::class);

        $ldap->shouldReceive('bind')->once()->withArgs(['prefix.johndoe.suffix', 'secret'])->andReturn(true);

        $events = new Dispatcher();

        $firedBinding = false;
        $firedBound = false;
        $firedAttempting = false;
        $firedPassed = false;

        $events->listen(Binding::class, function (Binding $event) use (&$firedBinding) {
            $this->assertEquals('prefix.johndoe.suffix', $event->getUsername());
            $this->assertEquals('secret', $event->getPassword());

            $firedBinding = true;
        });

        $events->listen(Bound::class, function (Bound $event) use (&$firedBound) {
            $this->assertEquals('prefix.johndoe.suffix', $event->getUsername());
            $this->assertEquals('secret', $event->getPassword());

            $firedBound = true;
        });

        $events->listen(Attempting::class, function (Attempting $event) use (&$firedAttempting) {
            $this->assertEquals('johndoe', $event->getUsername());
            $this->assertEquals('secret', $event->getPassword());

            $firedAttempting = true;
        });

        $events->listen(Passed::class, function (Passed $event) use (&$firedPassed) {
            $this->assertEquals('johndoe', $event->getUsername());
            $this->assertEquals('secret', $event->getPassword());

            $firedPassed = true;
        });

        $guard = new Guard($ldap, $config);

        $guard->setDispatcher($events);

        $this->assertTrue($guard->attempt('johndoe', 'secret', $bindAsUser = true));

        $this->assertTrue($firedBinding);
        $this->assertTrue($firedBound);
        $this->assertTrue($firedAttempting);
        $this->assertTrue($firedPassed);
    }

    public function test_all_auth_events_can_be_listened_to_with_wildcard()
    {
        $config = $this->mock(DomainConfiguration::class);

        $config
            ->shouldReceive('get')->withArgs(['account_prefix'])->once()->andReturn('prefix.')
            ->shouldReceive('get')->withArgs(['account_suffix'])->once()->andReturn('.suffix');

        $ldap = $this->mock(Ldap::class);

        $ldap->shouldReceive('bind')->once()->withArgs(['prefix.johndoe.suffix', 'secret'])->andReturn(true);

        $events = new Dispatcher();

        $totalFired = 0;

        $events->listen('Adldap\Auth\Events\*', function ($eventName) use (&$totalFired) {
            $totalFired++;
        });

        $guard = new Guard($ldap, $config);

        $guard->setDispatcher($events);

        $this->assertTrue($guard->attempt('johndoe', 'secret', $bindAsUser = true));

        $this->assertEquals(4, $totalFired);
    }
}
