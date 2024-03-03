<?php

declare(strict_types=1);

namespace Tests\DamianPhp\Support\String;

use Tests\BaseTest;
use DamianPhp\Support\String\Str;

class StrTest extends BaseTest
{
    private Str $str;

    public function setUp(): void
    {
        $this->str = new Str();
    }

    public function testActive(): void
    {
        $this->assertTrue(is_string($this->str->active('TEST')));
    }

    public function testActive2(): void
    {
        $this->assertTrue(is_string($this->str->active2('TEST')));
    }

    public function testAndIfHasQueryString(): void
    {
        $this->assertTrue(is_string($this->str->andIfHasQueryString(['page', 'pp'])));

        $this->assertTrue(is_string($this->str->andIfHasQueryString('page')));
    }

    public function testContains(): void
    {
        $test = 'testaaa';

        $this->assertTrue($this->str->contains($test, 'aaa'));

        $this->assertFalse($this->str->contains($test, 'aaabbb'));
    }

    public function testConvertCamelCaseToSnakeCase(): void
    {
        $test = 'testTest';

        $result = $this->str->convertCamelCaseToSnakeCase($test);

        $this->assertSame('test_test', $result);
    }

    public function testConvertSnakeCaseToCamelCase(): void
    {
        $test = 'test_test';

        $result = $this->str->convertSnakeCaseToCamelCase($test);

        $this->assertSame('TestTest', $result);
    }

    public function testExtract(): void
    {
        $this->assertTrue(is_string($this->str->extract('test_abc', 2)));
    }

    public function testExtractAlt(): void
    {
        $this->assertTrue(is_string($this->str->extractAlt('test_abc')));

        $this->assertTrue(is_string($this->str->extractAlt('test_abc', 2)));
    }

    public function testFirstEmail(): void
    {
        $emails = 'test1@live.fr, test2@live.fr, test3@live.fr';

        $this->assertTrue($this->str->firstEmail($emails) === 'test1@live.fr');
    }

    public function testFirstTel(): void
    {
        $tels = '06 06 06 06 01, 06 06 06 06 02, 06 06 06 06 03';

        $this->assertSame('06 06 06 06 01', $this->str->firstTel($tels));
    }

    public function testGetBreadcrumb(): void
    {
        $this->assertTrue(is_string(
            $this->str->getBreadcrumb([
                    'home_url' => 'Accueil',
                    'articles_url' => 'Articles',
                    'Article 1'
                ])
        ));

        $this->assertTrue(is_string(
            $this->str->getBreadcrumb([
                    'home_url' => 'Accueil',
                    'articles_url' => 'Articles',
                    'Article 1'
                ], [
                'class' => 'class_css'
            ])
        ));
    }

    public function testInputHiddenIfHasQueryString(): void
    {
        $this->assertTrue(is_string(Str::inputHiddenIfHasQueryString(['except' => ['except_test_1', 'except_test_2']])));
    }

    public function testInputHiddenIfHasQueryStringAddManually(): void
    {
        $this->assertTrue(is_string($this->str->inputHiddenIfHasQueryStringAddManually(['page', 'pp'])));

        $this->assertTrue(is_string($this->str->inputHiddenIfHasQueryStringAddManually('page')));
    }

    public function testRandom(): void
    {
        $this->assertTrue(is_string($this->str->random()));

        $this->assertTrue(is_string($this->str->random(10)));
        $this->assertSame(10, mb_strlen($this->str->random(10)));
    }

    public function testSnakePlural(): void
    {
        $test_ry = $this->str->snakePlural('category'); // si finit par "consonne + y" -> mettre "ies" à la place

        $test_ey = $this->str->snakePlural('gay'); // si finit par "voyelle + y" -> mettre "ys" à la place

        $test_o = $this->str->snakePlural('poteto'); // si finit par "o" -> mettre "oes" à la place

        $test_i = $this->str->snakePlural('poti'); // si non, ajouter un "s"

        $this->assertSame('ies', substr($test_ry, -3, mb_strlen($test_ry)));

        $this->assertSame('ys', substr($test_ey, -2, mb_strlen($test_ry)));

        $this->assertSame('oes', substr($test_o, -3, mb_strlen($test_ry)));

        $this->assertSame('s', substr($test_i, -1, mb_strlen($test_ry)));
    }

    public function testSurligneIfSearch(): void
    {
        $this->assertTrue(is_string($this->str->surligneIfSearch('Titre')));

        $this->assertTrue(is_string($this->str->surligneIfSearch('Titre', ['css_class' => 'class_test'])));
    }

    public function testTelInternationalFormat(): void
    {
        $this->assertSame('+33606060601', $this->str->telInternationalFormat('06 06 06 06 01'));

        $this->assertSame('+33 6 06 06 06 01', $this->str->telInternationalFormat('06 06 06 06 01', ['space' => true]));
    }
}
