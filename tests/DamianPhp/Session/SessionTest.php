<?php

namespace Tests\DamianPhp\Date;

use Tests\BaseTest;
use DamianPhp\Support\Facades\Session;

class SessionTest extends BaseTest
{
    public function testHas(): void
    {
        Session::count(); // Pour éventuelement céer "_url"

        $_SESSION = [];

        $this->assertSame(0, Session::count());

        $this->assertFalse(Session::has('name1'));
    }

    public function testPut()
    {
        Session::put('name1', 'Valeur1');

        Session::put('name2', 'Valeur2');

        Session::put('name3', 'Valeur3');

        $this->assertTrue(Session::has('name1'));
    }

    public function testGet(): void
    {
        $this->assertSame('Valeur1', Session::get('name1'));
    }

    public function testCount(): void
    {
        $this->assertSame(3, Session::count());
    }

    public function testAll(): void
    {
        $this->assertSame(3, count(Session::all()));
    }

    public function testKeys(): void
    {
        $this->assertSame(3, count(Session::keys()));
    }

    public function testDestroy(): void
    {
        Session::destroy('name1');

        $this->assertSame(2, Session::count());

        $_SESSION = [];
        
        $this->assertSame(0, Session::count());
    }

    public function testGetId(): void
    {
         $this->assertTrue(is_string(Session::getId()));
    }
}
