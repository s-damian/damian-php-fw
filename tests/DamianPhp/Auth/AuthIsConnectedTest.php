<?php

namespace Tests\DamianPhp\Auth;

use Tests\BaseTest;
use DamianPhp\Auth\Auth;
use DamianPhp\Support\Helper;
use DamianPhp\Auth\IsConnected;
use DamianPhp\Support\Facades\Date;
use DamianPhp\Support\Facades\Session;

class AuthIsConnectedTest extends BaseTest
{
    /*
    |--------------------------------------------------------------------------
    | Test "DamianPhp\Auth\Auth":
    |--------------------------------------------------------------------------
    */

    public function testConnect(): void
    {
        Session::count(); // Pour éventuelement céer "_url"

        $userModel = self::userModel();

        $_SESSION = [];

        $this->assertSame(0, Session::count());
        $this->assertFalse(Session::has('session_test'));

        $user = $userModel::find();

        $auth = new Auth($userModel::class);
        $auth->remember('cookie_test')->connect('session_test', [
            'id' => (int) $user->id,
            'email' => $user->email,
        ]);

        $this->assertTrue(Session::has('session_test'));
        $this->assertSame(1, Session::count());
    }

    /*
    |--------------------------------------------------------------------------
    | Test "DamianPhp\Auth\IsConnected" - Success:
    |--------------------------------------------------------------------------
    */

    public function testIsLoggedSuccess(): void
    {
        $isConnected = self::isConnected();

        $this->assertTrue($isConnected->isLogged());
    }

    /*
    |--------------------------------------------------------------------------
    | Fake User Model:
    |--------------------------------------------------------------------------
    */

    private static function userModel(): object
    {
        return new class () {
            public int $id;

            public ?string $date_last_connexion; // OBLIGATOIRE pour tester "DamianPhp\Auth\Auth".

            public string $email;

            public string $password;

            public ?string $remember_token; // OBLIGATOIRE pour tester "DamianPhp\Auth\Auth".

            private const FAKE_ID = 1;

            final public static function load(): self
            {
                return new self();
            }

            final public function select(): self
            {
                return $this;
            }

            final public function where(): self
            {
                return $this;
            }

            final public function limit(): self
            {
                return $this;
            }

            final public function update()
            {
                $this->date_last_connexion = Date::getDateTimeFormat();
            }

            public static function find(): self
            {
                $user = new self();
                $user->id = self::FAKE_ID;
                $user->email = 'user-model@gmail.com';

                return $user;
            }
        };
    }

    /*
    |--------------------------------------------------------------------------
    | End:
    |--------------------------------------------------------------------------
    */

    public function testEnd(): void
    {
        Session::destroy('session_test');

        $_SESSION = [];

        $this->assertSame(0, Session::count());
    }

    /*
    |--------------------------------------------------------------------------
    | Test "DamianPhp\Auth\IsConnected" - Error:
    |--------------------------------------------------------------------------
    */

    public function testIsLoggedError(): void
    {
        $isConnected = self::isConnected();

        $this->assertFalse($isConnected->isLogged());
    }

    private static function isConnected(): IsConnected
    {
        $isConnected = new IsConnected(self::userModel()::class);

        $isConnected->session('session_test', ['id', 'email'], ['id', 'email'])
            ->cookie('cookie_test')
            ->urlToredirectIfFalse(Helper::config('app')['url']);

        return $isConnected;
    }
}
