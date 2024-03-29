<?php

declare(strict_types=1);

namespace Tests\DamianPhp\Http\Response;

use Tests\BaseTest;
use DamianPhp\Http\Response\Json;

class JsonTest extends BaseTest
{
    private const ARRAY_TEST = [
        'aaa' => 'Valeur A',
        'bbb' => 'Valeur B',
        'ccc' => 'Valeur C',
    ];

    private Json $json;

    public function setUp(): void
    {
        $this->json = new Json();
    }

    public function testEncode(): void
    {
        $encode = $this->json->encode(self::ARRAY_TEST);

        $this->assertSame('{"aaa":"Valeur A","bbb":"Valeur B","ccc":"Valeur C"}', $encode);
    }

    public function testDecode(): void
    {
        $this->assertTrue(is_string($this->json->decode('"Abc"')));

        $decode = $this->json->decode('{"aaa":"Valeur A","bbb":"Valeur B","ccc":"Valeur C"}');

        $this->assertTrue(is_object($decode));
        $this->assertSame(self::ARRAY_TEST, (array) $decode);
    }
}
