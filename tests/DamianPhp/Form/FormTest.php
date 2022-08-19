<?php

namespace Tests\DamianPhp\Date;

use Tests\BaseTest;
use DamianPhp\Form\Form;

/**
 * On test que les méthodes de la classe Form renvoyent bien des string.
 * On fait les test sans préciser les paramètres optionels, et on refait le test en précisant tout les paramètres possibles
 */
class FormTest extends BaseTest
{
    private Form $form;

    public function setUp(): void
    {
        $this->form = new Form();
    }

    public function testOpen(): void
    {
        $this->assertTrue(is_string($this->form->open(['action' => 'action', 'method' => 'put', 'class' => 'form-edit', 'files' => true])));

        $this->assertTrue(is_string($this->form->open()));
    }

    public function testLabel(): void
    {
        $this->assertTrue(is_string($this->form->label('for', 'Text :', ['id' => 'id-css', 'class' => 'class-css', 'style' => 'margin-bottom: 20px;'])));

        $this->assertTrue(is_string($this->form->label('for', 'Text :')));
    }

    public function testText(): void
    {
        $this->assertTrue(is_string($this->form->text('name', 'Value')));

        $this->assertTrue(is_string($this->form->text('name', 'Value', [
            'id' => 'id-css', 'class' => 'class-css', 'style' => 'margin-bottom: 20px;', 'placeholder' => 'Placeholder', 'required' => true,
        ])));
    }

    public function testEmail(): void
    {
        $this->assertTrue(is_string($this->form->email('name', 'Value')));

        $this->assertTrue(is_string($this->form->email('name', 'Value', [
            'id' => 'id-css', 'class' => 'class-css', 'style' => 'margin-bottom: 20px;', 'placeholder' => 'Placeholder', 'required' => true,
        ])));
    }

    public function testSearch(): void
    {
        $this->assertTrue(is_string($this->form->search('name', 'Value')));

        $this->assertTrue(is_string($this->form->search('name', 'Value', [
            'id' => 'id-css', 'class' => 'class-css', 'style' => 'margin-bottom: 20px;', 'placeholder' => 'Placeholder', 'required' => true,
        ])));
    }

    public function testUrl(): void
    {
        $this->assertTrue(is_string($this->form->url('name', 'Value')));

        $this->assertTrue(is_string($this->form->url('name', 'Value', [
            'id' => 'id-css', 'class' => 'class-css', 'style' => 'margin-bottom: 20px;', 'placeholder' => 'Placeholder', 'required' => true,
        ])));
    }

    public function testTel(): void
    {
        $this->assertTrue(is_string($this->form->tel('name', 'Value')));

        $this->assertTrue(is_string($this->form->tel('name', 'Value', [
            'id' => 'id-css', 'class' => 'class-css', 'style' => 'margin-bottom: 20px;', 'placeholder' => 'Placeholder', 'required' => true,
        ])));
    }

    public function testPassword(): void
    {
        $this->assertTrue(is_string($this->form->password('name')));

        $this->assertTrue(is_string($this->form->password('name', [
            'id' => 'id-css', 'class' => 'class-css', 'style' => 'margin-bottom: 20px;', 'placeholder' => 'Placeholder', 'required' => true,
        ])));
    }

    public function testHidden(): void
    {
        $this->assertTrue(is_string($this->form->hidden('name', 'Value')));

        $this->assertTrue(is_string($this->form->hidden('name', 'Value', [
            'id' => 'id-css', 'class' => 'class-css', 'style' => 'margin-bottom: 20px;',
        ])));
    }

    public function testCheckbox(): void
    {
        $this->assertTrue(is_string($this->form->checkbox('name', 'Value')));

        $this->assertTrue(is_string($this->form->checkbox('name', 'Value', [
            'id' => 'id-css', 'class' => 'class-css', 'style' => 'margin-bottom: 20px;', 'checked' => true,
        ])));
    }

    public function testRadio(): void
    {
        $this->assertTrue(is_string($this->form->radio('name', 'Value')));

        $this->assertTrue(is_string($this->form->radio('name', 'Value', [
            'id' => 'id-css', 'class' => 'class-css', 'style' => 'margin-bottom: 20px;', 'checked' => true,
        ])));
    }

    public function testNumber(): void
    {
        $this->assertTrue(is_string($this->form->number('name', 'Value')));

        $this->assertTrue(is_string($this->form->number('name', 'Value', [
            'id' => 'id-css', 'class' => 'class-css', 'style' => 'margin-bottom: 20px;', 'step' => "2", 'min' => 10, 'max' => 260,
        ])));
    }

    public function testRange(): void
    {
        $this->assertTrue(is_string($this->form->range('name', 'Value')));

        $this->assertTrue(is_string($this->form->range('name', 'Value', [
            'id' => 'id-css', 'class' => 'class-css', 'style' => 'margin-bottom: 20px;', 'step' => "2", 'min' => 10, 'max' => 260,
        ])));
    }

    public function testSubmit(): void
    {
        $this->assertTrue(is_string($this->form->submit()));

        $this->assertTrue(is_string($this->form->submit('name', 'Value', [
            'id' => 'idsubmit', 'style' => 'margin-bottom: 20px;',
        ])));
    }

    public function testFile(): void
    {
        $this->assertTrue(is_string($this->form->file('namefiles')));

        $this->assertTrue(is_string($this->form->file('namefiles[]', [
            'id' => 'idfile', 'class' => 'classfile', 'style' => 'margin-bottom: 20px;', 'multiple' => true,
        ])));
    }

    public function testButton(): void
    {
        $this->assertTrue(is_string($this->form->button()));

        $this->assertTrue(is_string($this->form->button('Text Button', [
            'name' => 'Envoyer', 'style' => 'margin-bottom: 20px;',
        ])));
    }

    public function testTextarea(): void
    {
        $this->assertTrue(is_string($this->form->textarea('name', 'Value')));

        $this->assertTrue(is_string($this->form->textarea('name', 'Value', [
            'id' => 'id-css', 'class' => 'class-css', 'style' => 'margin-bottom: 20px;', 'placeholder' => 'Ecrivez...', 'required' => true,
        ])));
    }

    public function testSelect(): void
    {
        $this->assertTrue(is_string($this->form->select('name', [
            1 => 'Publié',
            2 => 'Brouillon',
            3 => 'Corbeille',
        ])));

        $this->assertTrue(is_string($this->form->select('name', [
            1 => 'Publié',
            2 => 'Brouillon',
            3 => 'Corbeille',
        ], 2, [
            'class' => 'classselect',
            'id' => 'idselect',
            'autosubmit' => 'idform',
            'style' => 'margin-bottom: 20px;',
        ])));
    }

    public function testSelectWithOptgroup(): void
    {
        $this->assertTrue(is_string($this->form->select('nameselect', [
            'articles' => [
                1 => 'Publié',
                2 => 'Brouillon',
                3 => 'Corbeille',
            ],
            'pages'=>[
                4 => 'Publié',
                5 => 'Brouillon',
                6 => 'Corbeille',
            ],
        ], 2, [
            'class' => 'classselect',
            'id' => 'idselect',
        ])));
    }

    public function testClose(): void
    {
        $this->assertTrue(is_string($this->form->close()));
    }
}
