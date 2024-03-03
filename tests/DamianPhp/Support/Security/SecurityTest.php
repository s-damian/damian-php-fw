<?php

declare(strict_types=1);

namespace Tests\DamianPhp\Support\Security;

use Tests\BaseTest;
use DamianPhp\Support\Security\Security;

class SecurityTest extends BaseTest
{
    private Security $security;

    public function setUp(): void
    {
        $this->security = new Security();
    }

    public function testE(): void
    {
        $this->assertTrue(is_string($this->security->e('Test')));
    }

    public function testNoCrlf(): void
    {
        $email = 'test@live.fr<br>test@live.fr';

        $emailNoCrlf = $this->security->noCrlf($email);

        $this->assertTrue(mb_strpos($email, '<br>') !== false);

        $this->assertTrue(mb_strpos($emailNoCrlf, '<br>') === false);
    }

    public function testHash(): void
    {
        $private = 'abc';

        $this->assertTrue($private !== $this->security->hash($private));
    }

    public function testgetExtFile(): void
    {
        $file = 'test.abc.png';

        $this->assertSame('png', $this->security->getExtFile($file));
    }
}
