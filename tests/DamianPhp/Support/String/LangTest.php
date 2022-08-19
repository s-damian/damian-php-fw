<?php

namespace Tests\DamianPhp\Support\String;

use Tests\BaseTest;
use DamianPhp\Support\String\Lang;

class LangTest extends BaseTest
{
    private Lang $lang;

    public function setUp(): void
    {
        $this->lang = new Lang();
    }

    public function testLang(): void
    {
        $this->assertFalse($this->lang->hasCountryLanguage('enaaaaa'));

        $this->assertTrue(is_string($this->lang->getHreflang()));

        $this->assertTrue(is_string($this->lang->getImglang()));

        $this->assertTrue(is_string($this->lang->getImglangActive()));
    }
}
