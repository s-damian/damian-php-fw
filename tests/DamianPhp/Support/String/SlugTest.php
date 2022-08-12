<?php

namespace Tests\DamianPhp\Support\String;

use Tests\BaseTest;
use DamianPhp\Support\String\Slug;

class SlugTest extends BaseTest
{
    private Slug $slug;

    private const STRING_TO_TEST = 'Mot1 mot2 mot3';

    public function setUp(): void
    {
       $this->slug = new Slug();
    }

    public function testCreate(): void
    {
        $this->assertSame('mot1-mot2-mot3', $this->slug->create(self::STRING_TO_TEST));
    }

    public function testCreateKeywords(): void
    {
        $this->assertSame('mot1, mot2, mot3', $this->slug->createKeywords(self::STRING_TO_TEST));
    }
}
