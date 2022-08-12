<?php

namespace Tests\DamianPhp\Validation;

use Tests\BaseTest;
use DamianPhp\Pagination\Pagination;

class PaginationTest extends BaseTest
{
    /**
     * Est appellée après chaque testMethod() de cette classe et de classes enfants
     * (si on met un tearDown() dans une classe enfant, c'est celle de la classe enfant qui sera appelé avant)
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $_GET = [];
    }

    public function testPagination(): void
    {
        $_GET['page'] = 4;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertSame(45, $pagination->getOffset()); // on débute bien le LIMIT à partir de l'élément 45. a la valeur de : getFrom() - (moins) 1
        $this->assertSame(15, $pagination->getLimit()); // il y a bien 15 éléments d'affichés sur cette page en cours
        $this->assertSame(100, $pagination->getCount()); // Nombre total d'éléments sur lesquels paginer
        $this->assertSame(15, $pagination->getCountOnCurrentPage()); // Même valeur que getLimit()
        $this->assertSame(46, $pagination->getFrom()); // on débute bien la pagination à l'élément 46
        $this->assertSame(60, $pagination->getTo()); // on finit bien la pagination à l'élément 60
        $this->assertSame(4, $pagination->getCurrentPage()); // même valeur que $_GET['page']
        $this->assertSame(7, $pagination->getNbPages()); // 100/15 = 6.66666666667
        $this->assertSame(15, $pagination->getDefaultPerPage());
        $this->assertSame(null, $pagination->getGetPP()); // pas de $_GET['pp'] dans l'URL
        $this->assertTrue($pagination->hasMorePages());
        $this->assertFalse($pagination->isFirstPage()); // false car on est sur la page 4
        $this->assertFalse($pagination->isLastPage()); // false car on est sur la page 4
        $this->assertTrue(is_string($pagination->render()));
        $this->assertTrue(is_string($pagination->perPage()));
        $this->assertSame(1, $pagination->getPageStart()); // doit etre positionné ici après render()
        $this->assertSame(7, $pagination->getPageEnd()); // 100/15 = 6.66666666667
    }

    /**
     * On test les options (avec les valeurs par défaut)
     */
    public function testPaginationWithOptionsDefault(): void
    {
        $_GET['page'] = 2;

        $pagination = new Pagination();

        $pagination->paginate(100);
        
        $this->assertSame(15, $pagination->getPerPage());
        $this->assertSame(5, $pagination->getNumberLinks());
        $this->assertSame('block-pagination', $pagination->getCssClassP());
        $this->assertSame('active', $pagination->getCssClassLinkActive());
        $this->assertSame('per-page', $pagination->getCssIdPP());
    }

    /**
     * On test les options (on change toutes les valeurs)
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

        $this->assertSame(10, $pagination->getPerPage());
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

    public function testNotHasMorePage(): void
    {
        $_GET['page'] = 3;

        $pagination = new Pagination();

        $pagination->paginate(28);

        $this->assertTrue($pagination->hasMorePages());
    }
}
