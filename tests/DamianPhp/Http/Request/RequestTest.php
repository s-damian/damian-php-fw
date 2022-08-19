<?php

namespace Tests\DamianPhp\Http\Request;

use Tests\BaseTest;
use DamianPhp\Http\Request\Request;

class RequestTest extends BaseTest
{
    public function testGetPost(): void
    {
        $request = new Request();

        $this->assertSame(0, count($request->getPost()->all()));

        // Not null
        $_POST['title'] = 'Titre';

        $request = new Request();

        $this->assertSame(1, count($request->getPost()->all()));

        $this->assertSame(1, count($request->getPost()->keys()));

        $this->assertSame(1, $request->getPost()->count());

        $this->assertTrue($request->getPost()->has('title'));

        $this->assertSame('Titre', $request->getPost()->get('title'));

        $request->getPost()->set('title', 'Titre_new');
        $this->assertSame('Titre_new', $request->getPost()->get('title'));

        $request->getPost()->destroy('title');
        $this->assertFalse($request->getPost()->has('title'));

        $_POST = [];
    }

    public function testGetGet(): void
    {
        $request = new Request();

        $this->assertTrue(count($request->getGet()->all()) === 0);

        // Not null
        $_GET['title'] = 'Titre';

        $request = new Request();

        $this->assertSame(1, count($request->getGet()->all()));

        $this->assertSame(1, count($request->getGet()->keys()));

        $this->assertSame(1, $request->getGet()->count());

        $this->assertTrue($request->getGet()->has('title'));

        $this->assertSame('Titre', $request->getGet()->get('title'));

        $request->getGet()->set('title', 'Titre_new');
        $this->assertSame('Titre_new', $request->getGet()->get('title'));

        $request->getGet()->destroy('title');
        $this->assertFalse($request->getGet()->has('title'));

        $_GET = [];
    }

    public function testGetCookies(): void
    {
        $request = new Request();

        $this->assertTrue(count($request->getCookies()->all()) === 0);

        // Not null
        $_COOKIE['title'] = 'Titre';

        $request = new Request();

        $this->assertSame(1, count($request->getCookies()->all()));

        $this->assertSame(1, count($request->getCookies()->keys()));

        $this->assertSame(1, $request->getCookies()->count());

        $this->assertTrue($request->getCookies()->has('title'));

        $this->assertSame('Titre', $request->getCookies()->get('title'));

        $request->getCookies()->set('title', 'Titre_new');
        $this->assertSame('Titre_new', $request->getCookies()->get('title'));

        $request->getCookies()->destroy('title');
        $this->assertFalse($request->getCookies()->has('title'));

        $_COOKIE = [];
    }

    public function testGetServer(): void
    {
        $request = new Request();

        $this->assertTrue(is_array($request->getServer()->all()));

        $this->assertTrue(is_array($request->getServer()->keys()));

        $this->assertTrue($request->getServer()->count() > 0);

        $this->assertTrue($request->getServer()->has('DOCUMENT_ROOT'));

        $this->assertSame('', $request->getServer()->get('DOCUMENT_ROOT'));
    }

    public function testGetFiles(): void
    {
        $_FILES['file_input']['name'] = 'Name';

        $request = new Request();

        $this->assertTrue($request->getFiles()->has('file_input'));

        $this->assertSame('Name', $request->getFiles()->get('file_input')['name']);

        $_FILES = [];
    }

    public function testIsMethod(): void
    {
        $request = new Request();

        $this->assertFalse($request->isMethod('GET'));
        $this->assertFalse($request->isInMethods(['GET']));

        $this->assertTrue($request->isMethod('cli'));
        $this->assertTrue($request->isInMethods(['cli']));

        $this->assertFalse($request->isGet());
        $this->assertFalse($request->isPost());
        $this->assertFalse($request->isPut());
        $this->assertFalse($request->isDelete());
        $this->assertFalse($request->isPatch());
        $this->assertFalse($request->isHead());
        $this->assertFalse($request->isOptions());
        $this->assertFalse($request->isAjax());
        $this->assertTrue($request->isCli());
    }

    public function testGetMethodsAllowedForInputMethod(): void
    {
        $request = new Request();

        $this->assertSame(3, count($request->getMethodsAllowedForInputMethod()));
    }

    public function testGetMethod(): void
    {
        $request = new Request();

        $this->assertTrue(is_string($request->getMethod()));
        $this->assertSame('', $request->getMethod()); // en testing (en cli), ça vaut ''
    }

    public function testGetRequestMethod(): void
    {
        $request = new Request();

        $this->assertTrue(is_string($request->getRequestMethod()));
        $this->assertSame('', $request->getRequestMethod()); // en testing (en cli), ça vaut ''
    }

    public function testGetUrlCurrent(): void
    {
        $request = new Request();

        $this->assertTrue(is_string($request->getUrlCurrent()));
    }

    public function testGetFullUrlWithQuery()
    {
        $request = new Request();

        $this->assertSame('://?page=1', $request->getFullUrlWithQuery(['page' => 1]));
    }

    public function testBuildQuery()
    {
        $request = new Request();

        $this->assertSame('page=1', $request->buildQuery(['page' => 1]));

        $this->assertSame('page=1&orderby=title&order=asc', $request->buildQuery(['page' => 1, 'orderby' => 'title', 'order' => 'asc']));
    }
}
