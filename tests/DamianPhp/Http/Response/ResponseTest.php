<?php

namespace Tests\DamianPhp\Http\Response;

use Tests\BaseTest;
use DamianPhp\Http\Response\Response;

class ResponseTest extends BaseTest
{
    private Response $response;

    public function setUp(): void
    {
        $this->response = new Response();
    }

    public function testGetHttpResponseCode(): void
    {
        $this->assertTrue(is_int($this->response->getHttpResponseCode()));
    }

    public function testAlertSuccess(): void
    {
        $this->assertTrue(is_string($this->response->alertSuccess('Ok !')));
    }

    public function testAlertError(): void
    {
        $this->assertTrue(is_string($this->response->alertError('Erreur !')));
    }

    public function testSetAlert(): void
    {
        $this->assertTrue(is_string($this->response->setAlert('class-css', 'Message !')));
    }
}
