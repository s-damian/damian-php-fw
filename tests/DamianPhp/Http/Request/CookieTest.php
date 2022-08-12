<?php

namespace Tests\DamianPhp\Http\Request;

use Tests\BaseTest;
use DamianPhp\Http\Request\Cookie;

class CookieTest extends BaseTest
{
    public function testCookies(): void
    {
        $cookie = new Cookie();

        $this->assertFalse($cookie->has('cookie_a'));

        $_COOKIE['cookie_a'] = 'aaa';

        $cookie = new Cookie();
        
        $this->assertTrue($cookie->has('cookie_a'));
        $this->assertSame('aaa', $cookie->get('cookie_a'));

        $_COOKIE = [];
        
        $cookie = new Cookie();

        $this->assertFalse($cookie->has('cookie_a'));
    }
}
