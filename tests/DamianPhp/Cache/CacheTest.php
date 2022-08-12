<?php

namespace Tests\DamianPhp\Cache;

use stdClass;
use Tests\BaseTest;
use DamianPhp\Cache\Cache;

class CacheTest extends BaseTest
{
    public function testPut(): void
    {
        $cacheMock = $this->getMockBuilder(Cache::class)->onlyMethods(['get', 'put'])->getMock();

        $cacheMock->expects($this->once())->method('put')->with('file_test', 'Hello');

        /** @var Cache $cacheMock */
        $cacheMock->put('file_test', 'Hello');
    }

    public function testGet(): void
    {
        $cacheMock = $this->getMockBuilder(Cache::class)->onlyMethods(['get'])->getMock();
        
        $cacheMock->method('get')->willReturn('En cache');

        $this->expectOutputString('En cache');

        /** @var Cache $cacheMock */
        echo $cacheMock->get('file_test');

        $cacheMock2 = $this->getMockBuilder(Cache::class)->onlyMethods(['get'])->getMock();

        $cacheMock2->method('get')->willReturn(false);

        $this->assertFalse($cacheMock2->get('file_test'));
    }

    public function testGetToObject(): void
    {
        $cacheMock = $this->getMockBuilder(Cache::class)->onlyMethods(['getToObject'])->getMock();
        
        $cacheMock->method('getToObject')->willReturn(new stdClass());

        /** @var Cache $cacheMock */
        $test = $cacheMock->getToObject('file_test');

        $this->assertTrue(is_object($test));

        $cacheMock2 = $this->getMockBuilder(Cache::class)->onlyMethods(['getToObject'])->getMock();
        
        $cacheMock2->method('getToObject')->willReturn(false);

        /** @var Cache $cacheMock2 */
        $test2 = $cacheMock2->getToObject('file_test');

        $this->assertFalse($test2);
    }

    public function testGetToArray(): void
    {
        $cacheMock = $this->getMockBuilder(Cache::class)->onlyMethods(['getToArray'])->getMock();
        
        $cacheMock->method('getToArray')->willReturn(['aaa'=>1]);

        /** @var Cache $cacheMock */
        $test = $cacheMock->getToArray('file_test');

        $this->assertTrue(is_array($test));

        $cacheMock2 = $this->getMockBuilder(Cache::class)->onlyMethods(['getToArray'])->getMock();
        
        $cacheMock2->method('getToArray')->willReturn(false);

        /** @var Cache $cacheMock2 */
        $test2 = $cacheMock2->getToArray('file_test');

        $this->assertFalse($test2);
    }

    public function testRemember(): void
    {
        $cacheMock = $this->getMockBuilder(Cache::class)->onlyMethods(['remember'])->getMock();
        
        $cacheMock->method('remember')->willReturn('En cache');

        $this->expectOutputString('En cache');

        /** @var Cache $cacheMock */
        $test = $cacheMock->remember('file_test', 123, function () {
            echo 'Test !';
        });

        echo $test;
    }

    public function testHas(): void
    {
        $cacheMock = $this->getMockBuilder(Cache::class)->onlyMethods(['has'])->getMock();
        
        $cacheMock->method('has')->willReturn(false);

        /** @var Cache $cacheMock */
        $this->assertFalse($cacheMock->has('file_test'));
    }
}
