<?php

namespace Tests\DamianPhp\Http\Response;

use Tests\BaseTest;
use DamianPhp\Http\Response\Json;

class JsonTest extends BaseTest
{
    private Json $json;

    private const ARRAY_TEST = [
        'aaa' => 'Valeur A',
        'bbb' => 'Valeur B',
        'bbb' => 'Valeur C',
    ];

    public function setUp(): void
    {
       $this->json = new Json();
    }

    public function testEncode(): void
    {
        $encode = $this->json->encode(self::ARRAY_TEST);

        $this->assertSame('{"aaa":"Valeur A","bbb":"Valeur C"}', $encode);
    }

    public function testDecode(): void
    {
        $this->assertTrue(is_string($this->json->decode('"Abc"')));

        $decode = $this->json->decode('{"aaa":"Valeur A","bbb":"Valeur C"}');

        $this->assertTrue(is_object($decode));
        $this->assertSame(self::ARRAY_TEST, (array) $decode);
    }
}
