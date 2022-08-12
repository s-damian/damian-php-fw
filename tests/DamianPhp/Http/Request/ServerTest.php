<?php

namespace Tests\DamianPhp\Http\Request;

use Tests\BaseTest;
use DamianPhp\Http\Request\Server;

class ServerTest extends BaseTest
{
    private Server $server;

    public function setUp(): void
    {
       $this->server = new Server();
    }

    public function testGet(): void
    {
        $this->assertSame('', $this->server->get('key_a'));
    }

    public function testGetMethod(): void
    {
        $this->assertTrue(is_string($this->server->getMethod()));
    }

    public function testGetRequestUri(): void
    {
        $this->assertTrue(is_string($this->server->getRequestUri()));
    }

    public function testGetUri(): void
    {
        $this->assertTrue(is_string($this->server->getUri()));
    }

    public function testGetServerName(): void
    {
        $this->assertTrue(is_string($this->server->getServerName()));
    }

    public function testGetServerSoftware(): void
    {
        $this->assertTrue(is_string($this->server->getServerSoftware()));
    }

    public function testGetHttpHost(): void
    {
        $this->assertTrue(is_string($this->server->getHttpHost()));
    }

    public function testGetUrlCurrent(): void
    {
        $this->assertTrue(is_string($this->server->getUrlCurrent()));
    }

    public function testGetDomainName(): void
    {
        $this->assertTrue(is_string($this->server->getDomainName()));
    }

    public function testGetDocumentRoot(): void
    {
        $this->assertTrue(is_string($this->server->getDocumentRoot()));
    }

    public function testGetRequestScheme(): void
    {
        $this->assertTrue(is_string($this->server->getRequestScheme()));
    }

    public function testGetIp(): void
    {
        $this->assertTrue(is_string($this->server->getIp()));
    }

    public function testGetHttpUserAgent(): void
    {
        $this->assertTrue(is_string($this->server->getHttpUserAgent()));
    }
}
