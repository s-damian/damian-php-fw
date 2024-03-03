<?php

declare(strict_types=1);

namespace Tests\DamianPhp\Date;

use Tests\BaseTest;
use DamianPhp\Hashing\Hash;

class HashTest extends BaseTest
{
    private Hash $hash;

    public function setUp(): void
    {
        $this->hash = new Hash();
    }

    public function testHash(): void
    {
        $password = 'abc';

        $this->assertTrue($password !== $this->hash->hash($password));
    }

    public function testVerify(): void
    {
        $password = 'abc';

        $this->assertTrue($this->hash->verify($password, $this->hash->hash($password)));
    }

    public function testNeedsRehash(): void
    {
        $password = 'abc';

        $this->assertFalse($this->hash->needsRehash($this->hash->hash($password)));
    }
}
