<?php

declare(strict_types=1);

namespace Tests\DamianPhp\Lang;

use Tests\BaseTest;
use DamianPhp\Support\Helper;

class LangTest extends BaseTest
{
    public function testLang(): void
    {
        $this->assertTrue(is_string(lang('pagination.all')));
        $this->assertTrue(is_string(lang('pagination')['all']));

        $this->assertTrue(is_string(Helper::lang('pagination.all')));
        $this->assertTrue(is_string(Helper::lang('pagination')['all']));
    }
}
