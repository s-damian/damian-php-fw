<?php

namespace Tests\DamianPhp\Date;

use Tests\BaseTest;
use DamianPhp\Routing\Router;

class RouterTest extends BaseTest
{
    public function testLang(): void
    {
        $router = new Router();

        $this->assertTrue(is_string($router->getLang()));
    }

    public function testGet(): void
    {
        $router = new Router();

        $router->get(
            '/contact',
            'App\Http\Controllers\Front\Pages\ContactController@getContact',
            ['name' => 'contact']
        );

        $this->assertSame('/contact', $router->url('contact'));
    }

    public function testHead(): void
    {
        $router = new Router();

        $router->head(
            '/contact',
            'App\Http\Controllers\Front\Pages\ContactController@getContact',
            ['name' => 'contact_head']
        );

        $this->assertSame('/contact', $router->url('contact_head'));
    }

    public function testPost(): void
    {
        $router = new Router();

        $router->post(
            '/contact',
            'App\Http\Controllers\Front\Pages\ContactController@getContact',
            ['name' => 'contact_post']
        );

        $this->assertSame('/contact', $router->url('contact_post'));
    }

    public function testPut(): void
    {
        $router = new Router();

        $router->put(
            '/contact',
            'App\Http\Controllers\Front\Pages\ContactController@getContact',
            ['name' => 'contact_put']
        );

        $this->assertSame('/contact', $router->url('contact_put'));
    }

    public function testPatch(): void
    {
        $router = new Router();

        $router->patch(
            '/contact',
            'App\Http\Controllers\Front\Pages\ContactController@getContact',
            ['name' => 'contact_patch']
        );

        $this->assertSame('/contact', $router->url('contact_patch'));
    }

    public function testDelete(): void
    {
        $router = new Router();

        $router->delete(
            '/contact',
            'App\Http\Controllers\Front\Pages\ContactController@getContact',
            ['name' => 'contact_delete']
        );

        $this->assertSame('/contact', $router->url('contact_delete'));
    }

    public function testOptions(): void
    {
        $router = new Router();

        $router->options(
            '/contact',
            'App\Http\Controllers\Front\Pages\ContactController@getContact',
            ['name' => 'contact_options']
        );

        $this->assertSame('/contact', $router->url('contact_options'));
    }

    public function testAny(): void
    {
        $router = new Router();

        $router->any(
            '/contact',
            'App\Http\Controllers\Front\Pages\ContactController@getContact'
        );

        $this->assertTrue(true);
    }

    public function testMatch(): void
    {
        $router = new Router();

        $router->match(
            ['GET', 'POST'],
            '/contact',
            'App\Http\Controllers\Front\Pages\ContactController@getContact'
        );

        $this->assertTrue(true);
    }

    public function testGetWithGroupPrefix(): void
    {
        $router = new Router();

        $router->group(['prefix' => '/admin'], function () use ($router) {
            $router->get(
                '/home',
                'App\Http\Controllers\Admin\Pages\HomeController@getHome',
                ['name' => 'admin_home']
            );
        });

        $this->assertSame('/admin/home', $router->url('admin_home'));
    }

    public function testGetWithManyGroupPrefix(): void
    {
        $router = new Router();

        $router->group(['prefix' => '/admin'], function () use ($router) {
            $router->group(['prefix' => '/test'], function () use ($router) {
                $router->group(['prefix' => '/fr'], function () use ($router) {
                    $router->get(
                        '/home',
                        'App\Http\Controllers\Admin\Pages\HomeController@getHome',
                        ['name' => 'admin_home']
                    );
                });
            });
        });

        $this->assertSame('/admin/test/fr/home', $router->url('admin_home'));
        $this->assertSame('App\Http\Controllers\Admin\Pages\HomeController@getHome', $router->calledCallable('admin_home'));
    }

    public function testGetWithGroupNamespace(): void
    {
        $router = new Router();

        $router->group(['namespace' => 'App\Http\Controllers\Site\\'], function () use ($router) {
            $router->get(
                '/home',
                'HomeController@getHome',
                ['name' => 'admin_home']
            );
        });

        $this->assertSame('/home', $router->url('admin_home'));
        $this->assertSame('App\Http\Controllers\Site\HomeController@getHome', $router->calledCallable('admin_home'));
    }

    public function testGetWithMAnyGroupNamespace(): void
    {
        $router = new Router();

        $router->group(['namespace' => 'App\Http\\'], function () use ($router) {
            $router->group(['namespace' => 'Controllers\Site\\'], function () use ($router) {
                $router->get(
                    '/home',
                    'HomeController@getHome',
                    ['name' => 'admin_home']
                );
            });
        });

        $this->assertSame('/home', $router->url('admin_home'));
        $this->assertSame('App\Http\Controllers\Site\HomeController@getHome', $router->calledCallable('admin_home'));
    }

    public function testGetWithGroupMiddleware(): void
    {
        $router = new Router();

        $router->group(['middleware' => ['middleware_a', 'middleware_b']], function () use ($router) {
            $router->get(
                '/home',
                'App\Http\Controllers\Site\HomeController@getHome',
                ['name' => 'admin_home']
            );
        });

        $this->assertSame('/home', $router->url('admin_home'));
        $this->assertSame('App\Http\Controllers\Site\HomeController@getHome', $router->calledCallable('admin_home'));
        $this->assertSame('middleware_a,middleware_b,', $router->calledMiddlewares('admin_home'));
    }
}
