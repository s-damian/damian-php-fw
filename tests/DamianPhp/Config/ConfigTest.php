<?php

namespace Tests\DamianPhp\Config;

use Tests\BaseTest;
use DamianPhp\Support\Helper;

class ConfigTest extends BaseTest
{
    public function testConfig(): void
    {
        $this->assertSame('testing', config('app.env'));
        $this->assertSame('testing', config('app')['env']);

        $this->assertSame('testing', Helper::config('app.env'));
        $this->assertSame('testing', Helper::config('app')['env']);
    }
}
