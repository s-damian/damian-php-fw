<?php

namespace Tests\DamianPhp\Support\Security;

use Tests\BaseTest;
use DamianPhp\Support\Security\CsrfToken;

class CsrfTokenTest extends BaseTest
{
    private CsrfToken $token;

    public function setUp(): void
    {
        $this->token = new CsrfToken();
    }

    public function testHtmlPost(): void
    {
        $this->assertTrue(is_string($this->token->htmlPost()));
    }

    public function testHtmlGet(): void
    {
        $this->assertTrue(is_string($this->token->htmlGet()));
    }
}
