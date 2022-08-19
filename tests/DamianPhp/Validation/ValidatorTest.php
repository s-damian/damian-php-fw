<?php

namespace Tests\DamianPhp\Validation;

use Tests\BaseTest;
use DamianPhp\Validation\Message;
use DamianPhp\Validation\Validator;

class ValidatorTest extends BaseTest
{
    public function testIsNotValid(): void
    {
        $_POST['test'] = '1';

        Validator::extend('test_strictly_equal', function ($input, $value, $parameters) {
            return ((string) $value === (string) $parameters);
        });

        $validator = new Validator();

        $validator->rules([
            'test' => [
                'alpha' => true,
                'test_strictly_equal' => '1',
            ],
        ]);

        $this->assertFalse($validator->isValid());
        $this->assertTrue($validator->hasError('test'));
        $this->assertFalse($validator->hasError('testzzz'));
        $this->assertTrue(is_string($validator->getError('test')));
        $this->assertTrue(is_array($validator->getErrors()));
        $this->assertSame(1, count($validator->getErrors()));
        $this->assertTrue($validator->getMessages() instanceof Message);
        $this->assertTrue(is_string($validator->getMessages()->toJson()));
        $this->assertTrue(is_string($validator->getMessages()->toHtml()));
        $this->assertTrue(is_string($validator->getErrorsHtml()));
        $this->assertTrue(is_string($validator->getErrorsJson()));

        $_POST = [];
    }

    public function testIsValid(): void
    {
        $_POST['test'] = 'aaa';

        $validator = new Validator();

        $validator->rules([
            'test' => [
                'alpha' => true,
            ],
        ]);

        $this->assertTrue($validator->isValid());
        $this->assertFalse($validator->hasError('test'));
        $this->assertSame(0, count($validator->getErrors()));

        $_POST = [];
    }

    public function testAddErrorWithInput(): void
    {
        $validator = new Validator();

        $validator->addErrorWithInput('test_input', 'Error !');

        $this->assertTrue($validator->hasError('test_input'));
        $this->assertFalse($validator->isValid());
    }

    public function testAddError(): void
    {
        $validator = new Validator();

        $validator->addError('Error !');

        $this->assertFalse($validator->isValid());
    }

    public function testAllRulesValide(): void
    {
        $_POST['input_alpha'] = 'aaa';
        $_POST['input_alpha_numeric'] = 'aaa123';
        $_POST['input_between'] = 5;
        $_POST['input_confirm'] = 'aaaaa';
        $_POST['input_date_time_not_after_now'] = '19/01/2015 19:50';
        $_POST['input_empty'] = '';
        $_POST['input_integer'] = '123';
        $_POST['input_in_array'] = '11';
        $_POST['input_format_date'] = '19/01/2017';
        $_POST['input_format_date_time'] = '19/01/2017 19:50';
        $_POST['input_format_email'] = 'test@live.fr';
        $_POST['input_format_ip'] = '22.233.56.22';
        $_POST['input_format_slug'] = 'slug-test';
        $_POST['input_format_url'] = 'https://www.url-test.com';
        $_POST['input_format_name_file'] = 'file-test.png';
        $_POST['input_format_postal_code'] = '38000';
        $_POST['input_format_tel'] = '06 06 06 06 06';
        $_POST['input_max'] = '123';
        $_POST['input_min'] = '123456789';
        $_POST['input_no_regex'] = 'afg5_';
        $_POST['input_not_in_array'] = 'aaazzz';
        $_POST['input_regex'] = 'ab';
        $_POST['input_required'] = 'adfr';

        $validator = $this->allRules();

        $this->assertTrue($validator->isValid());

        $_POST = [];
    }

    public function testAllRulesNotValide(): void
    {
        $_POST['input_alpha'] = 'aaa1';
        $_POST['input_alpha_numeric'] = 'aaa123_';
        $_POST['input_between'] = 22;
        $_POST['input_confirm'] = 'aaaaazzz';
        $_POST['input_date_time_not_after_now'] = '19/01/2050 19:50';
        $_POST['input_empty'] = 'zzz';
        $_POST['input_integer'] = '123aaaa';
        $_POST['input_in_array'] = '1';
        $_POST['input_format_date'] = 'sfefefef';
        $_POST['input_format_date_time'] = '21edfefe';
        $_POST['input_format_email'] = 'tesdddddfr';
        $_POST['input_format_ip'] = 'dz5554dz5dz';
        $_POST['input_format_slug'] = 'slugà-_/test';
        $_POST['input_format_url'] = 'deffeeffe';
        $_POST['input_format_name_file'] = 'file test';
        $_POST['input_format_postal_code'] = '35ddef';
        $_POST['input_format_tel'] = '06feef/EFl';
        $_POST['input_max'] = '123456789';
        $_POST['input_min'] = '123';
        $_POST['input_no_regex'] = '12';
        $_POST['input_not_in_array'] = 'aaa';
        $_POST['input_regex'] = 'abc44';
        $_POST['input_required'] = '';

        $validator = $this->allRules();

        $this->assertTrue($validator->hasError('input_alpha'));
        $this->assertTrue($validator->hasError('input_alpha_numeric'));
        $this->assertTrue($validator->hasError('input_between'));
        $this->assertTrue($validator->hasError('input_confirm'));
        $this->assertTrue($validator->hasError('input_date_time_not_after_now'));
        $this->assertTrue($validator->hasError('input_empty'));
        $this->assertTrue($validator->hasError('input_integer'));
        $this->assertTrue($validator->hasError('input_in_array'));
        $this->assertTrue($validator->hasError('input_format_date'));
        $this->assertTrue($validator->hasError('input_format_date_time'));
        $this->assertTrue($validator->hasError('input_format_email'));
        $this->assertTrue($validator->hasError('input_format_ip'));
        $this->assertTrue($validator->hasError('input_format_slug'));
        $this->assertTrue($validator->hasError('input_format_url'));
        $this->assertTrue($validator->hasError('input_format_name_file'));
        $this->assertTrue($validator->hasError('input_format_postal_code'));
        $this->assertTrue($validator->hasError('input_format_tel'));
        $this->assertTrue($validator->hasError('input_max'));
        $this->assertTrue($validator->hasError('input_min'));
        $this->assertTrue($validator->hasError('input_no_regex'));
        $this->assertTrue($validator->hasError('input_not_in_array'));
        $this->assertTrue($validator->hasError('input_regex'));
        $this->assertTrue($validator->hasError('input_required'));

        $_POST = [];
    }

    private function allRules(): Validator
    {
        $validator = new Validator();

        $validator->rules([
            'input_alpha' => ['label' => 'Titre H1', 'alpha' => true,],
            'input_alpha_numeric' => ['alpha_numeric' => true,],
            'input_between' => ['between' => [2, 9],],
            'input_date_time_not_after_now' => ['date_time_not_after_now' => true,],
            'input_confirm' => ['confirm' => [$_POST['input_confirm'], 'aaaaa']],
            'input_empty' => ['empty' => true],
            'input_integer' => ['integer' => true,],
            'input_in_array' => ['in_array' => ['11', '12', '13'],],
            'input_format_date' => ['format_date' => true,],
            'input_format_date_time' => ['format_date_time' => true,],
            'input_format_email' => ['format_email' => true,],
            'input_format_ip' => ['format_ip' => true,],
            'input_format_slug' => ['format_slug' => true,],
            'input_format_url' => ['format_url' => true,],
            'input_format_name_file' => ['format_name_file' => true,],
            'input_format_postal_code' => ['format_postal_code' => true,],
            'input_format_tel' => ['format_tel' => true,],
            'input_max' => ['max' => 5,],
            'input_min' => ['min' => 5,],
            'input_no_regex' => ['no_regex' => "/^[0-9]+$/",],
            'input_not_in_array' => ['not_in_array' => ['aaa', 'zzz']],
            'input_regex' => ['regex' => "/^[a-z]+$/",],
            'input_required' => ['required' => true,],
        ]);

        return $validator;
    }
}
