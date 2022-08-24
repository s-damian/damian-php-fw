<?php

namespace Tests;

use DamianPhp\Support\Helper;
use PHPUnit\Framework\TestCase;
use DamianPhp\Support\Facades\Str;

class BaseTest extends TestCase
{
    /**
     * Est appellée avant chaque testMethod() de cette classe et de classes enfants.
     */
    public function setUp(): void
    {
        parent::setUp();

        if (! file_exists(Helper::basePath('config/app.php'))) {
            exit('You cannot run the tests out of the skeleton.');
        }
    }

    /**
     * A basic test example.
     */
    public function testExample(): void
    {
        $this->assertTrue(true);
    }

    protected function randomSlug(int $nbChars = 30): string
    {
        return Str::random(35);
    }
}
