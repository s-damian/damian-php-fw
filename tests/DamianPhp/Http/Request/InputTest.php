<?php

namespace Tests\DamianPhp\Http\Request;

use Tests\BaseTest;
use DamianPhp\Http\Request\Input;

class InputTest extends BaseTest
{
    /**
     * Est appellée après chaque testMethod() de cette classe et de classes enfants.
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $_POST = [];
        $_GET = [];
        $_FILES = [];
    }

    public function testHasPost(): void
    {
        $input = new Input();

        $this->assertFalse($input->hasPost('title'));
    }

    public function testPost(): void
    {
        $_POST['title'] = 'Title';

        $input = new Input();

        $this->assertTrue($input->hasPost('title'));

        $this->assertSame('Title', $input->post('title'));
    }

    public function testHasGet(): void
    {
        $input = new Input();

        $this->assertFalse($input->hasGet('title'));
    }

    public function testGet(): void
    {
        $_GET['title'] = 'Title';

        $input = new Input();

        $this->assertTrue($input->hasGet('title'));

        $this->assertSame('Title', $input->get('title'));
    }

    public function testHasFile(): void
    {
        $input = new Input();

        $this->assertFalse($input->hasFile('file_input'));
    }

    public function testFile(): void
    {
        $_FILES['file_input']['name'] = 'Name';

        $input = new Input();

        $this->assertTrue($input->hasFile('file_input'));

        $this->assertSame('Name', $input->file('file_input')['name']);
    }
}
