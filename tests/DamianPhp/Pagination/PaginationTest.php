<?php

declare(strict_types=1);

namespace Tests\LaravelManPagination;

use Tests\BaseTest;
use DamianPhp\Pagination\Pagination;

/**
 * We do the "basic" tests.
 */
class PaginationTest extends BaseTest
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
     * Test Pagination Instance Methods.
     */
    public function testPagination(): void
    {
        $_GET['page'] = 4;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertSame(100, $pagination->getCount()); // nombre total d'éléments sur lesquels paginer
        $this->assertSame(15, $pagination->getCountOnCurrentPage()); // il y a 15 éléments d'affichés sur la page courante (même valeur que limit())
        $this->assertSame(46, $pagination->getFrom()); // on débute bien la pagination à l'élément 46
        $this->assertSame(60, $pagination->getTo()); // on finit bien la pagination à l'élément 60
        $this->assertSame(4, $pagination->getCurrentPage()); // même valeur que Request::offsetSet('page')
        $this->assertSame(7, $pagination->getNbPages()); // 100/15 = 6.66666666667
        $this->assertSame(15, $pagination->getPerPage()); // il y a bien 15 éléments d'affichés par page
        $this->assertTrue($pagination->hasPages()); // true car au dessus de 15 éléments, il faut bien paginer (ici on simule 100 éléments)
        $this->assertTrue($pagination->hasMorePages());
        $this->assertFalse($pagination->isFirstPage()); // false car on est sur la page 4
        $this->assertFalse($pagination->isLastPage()); // false car on est sur la page 4
        $this->assertTrue($pagination->isPage(4)); // true car on est sur la page 4

        $this->assertSame(15, $pagination->getLimit()); // il y a bien 15 éléments d'affichés sur cette page en cours
        $this->assertSame(45, $pagination->getOffset()); // on débute bien le LIMIT à partir de l'élément 45. a la valeur de : getFrom() - (moins) 1

        $this->assertTrue(is_string($pagination->render()));
        $this->assertTrue(is_string($pagination->perPageForm()));
    }

    /**
     * Test other public methods than Pagination Instance Methods.
     */
    public function testOtherPublicMethods()
    {
        $_GET['page'] = 4;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertTrue(is_string($pagination->render())); // pour que getPageStart() fonctionne

        $this->assertSame(15, $pagination->getDefaultPerPage());
        $this->assertSame(null, $pagination->getGetPP()); // pas de $_GET['pp'] dans l'URL
        $this->assertSame(1, $pagination->getPageStart()); // doit etre positionné ici après render()
        $this->assertSame(7, $pagination->getPageEnd()); // 100/15 = 6.66666666667
    }
}
