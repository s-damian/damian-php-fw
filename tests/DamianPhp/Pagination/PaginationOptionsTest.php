<?php

declare(strict_types=1);

namespace Tests\DamianPhp\Pagination;

use Tests\BaseTest;
use DamianPhp\Pagination\Pagination;

/**
 * We test the options of the constructor.
 */
class PaginationOptionsTest extends BaseTest
{
    /**
     * Est appellée après chaque testMethod() de cette classe et de classes enfants.
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $_GET = [];
    }

    /**
     * We test the options (with the default values).
     */
    public function testPaginationWithOptionsDefault(): void
    {
        $_GET['page'] = 2;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertSame(15, $pagination->getPerPage());
        $this->assertSame(5, $pagination->getNumberLinks());

        $arrayOptionsSelect = $pagination->getArrayOptionsSelect();
        $this->assertSame(6, count($arrayOptionsSelect));
        $this->assertSame(15, $arrayOptionsSelect[0]);
        $this->assertSame(30, $arrayOptionsSelect[1]);
        $this->assertSame(50, $arrayOptionsSelect[2]);
        $this->assertSame(100, $arrayOptionsSelect[3]);
        $this->assertSame(200, $arrayOptionsSelect[4]);
        $this->assertSame(300, $arrayOptionsSelect[5]);

        $this->assertSame('pagination', $pagination->getCssClassP());
        $this->assertSame('active', $pagination->getCssClassLinkActive());
        $this->assertSame('per-page-form', $pagination->getCssIdPP());
    }

    /**
     * We test the options (we change all the values).
     */
    public function testPaginationWithOptionsChanged(): void
    {
        $_GET['page'] = 2;

        $pagination = new Pagination([
            'pp' => 10,
            'number_links' => 3,
            'options_select' => [10, 20, 30],
            'css_class_p' => 'css_class_p_AAA',
            'css_class_link_active' => 'css_class_link_active_AAA',
            'css_id_pp' => 'css_id_pp_AAA',
        ]);

        $pagination->paginate(100);

        $this->assertSame(10, $pagination->getNbPages());
        $this->assertSame(3, $pagination->getNumberLinks());

        $arrayOptionsSelect = $pagination->getArrayOptionsSelect();
        $this->assertSame(3, count($arrayOptionsSelect));
        $this->assertSame(10, $arrayOptionsSelect[0]);
        $this->assertSame(20, $arrayOptionsSelect[1]);
        $this->assertSame(30, $arrayOptionsSelect[2]);

        $this->assertSame('css_class_p_AAA', $pagination->getCssClassP());
        $this->assertSame('css_class_link_active_AAA', $pagination->getCssClassLinkActive());
        $this->assertSame('css_id_pp_AAA', $pagination->getCssIdPP());
    }
}
