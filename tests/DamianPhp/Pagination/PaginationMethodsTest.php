<?php

namespace Tests\DamianPhp\Pagination;

use Tests\BaseTest;
use DamianPhp\Pagination\Pagination;

/**
 * Some methods are tested individually.
 */
class PaginationMethodsTest extends BaseTest
{
    /**
     * Est appellée avant chaque testMethod() de cette classe et de classes enfants.
     * PS : si on met un setUp() dans une classe enfant, c'est celle de la classe enfant qui sera appelé avant.
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $_GET = [];
    }

    public function testGetCurrentPage(): void
    {
        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertSame(1, $pagination->getCurrentPage()); // si $_GET['page'] n'existe pas, prend la valeur de 1 par défaut
        $this->assertTrue(! isset($_GET['page']));

        // On simule qu'on se positionne sur une page d'après la dernière page (donc on simule qu'on est une page qui n'existe pas).
        // Il existe que 7 pages, et on se positionne sur la 9ème.
        // PS (vs Pagination de Laravel) : ici getCurrentPage() n'a pas le même comportement que la pagination livré avec Laravel.
        // La pagination livré avec Laravel, quand on est sur une page d'après la dernière page, "getCurrentPage()" retorune la vvaleur passé dans l'URL.
        // Avec Laravel Man Pagination, getCurrentPage() prendra par défaut la page 1.
        $_GET['page'] = 9;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertSame(1, $pagination->getCurrentPage()); // prend la valeur de 1 par défaut
    }

    public function testHasPagesMethod(): void
    {
        $_GET['page'] = 1;

        $pagination = new Pagination();

        // Il y a 15 éléments à afficher par page.
        // Donc si à paginate() on lui indique (en param) qu'il y 16 (ou plus) élements à paginer, hasPages() retournera true.
        $pagination->paginate(16);

        $this->assertTrue($pagination->hasPages());
        $this->assertSame(2, $pagination->getNbPages()); // la pagination génère bien 2 pages

        // Il y a 15 éléments à afficher par page.
        // Donc si à paginate() on lui indique (en param) qu'il y 15 (ou moins) élements à paginer, hasPages() retournera false.
        $pagination->paginate(15);

        $this->assertFalse($pagination->hasPages());
        $this->assertSame(1, $pagination->getNbPages()); // la pagination génère bien qu'une seule page

        // Il y a 15 éléments à afficher par page.
        // Donc si à paginate() on lui indique (en param) qu'il y 15 (ou moins, dans cet test on met 14) élements à paginer, hasPages() retournera false.
        $pagination->paginate(14);

        $this->assertFalse($pagination->hasPages());
        $this->assertSame(1, $pagination->getNbPages()); // la pagination génère bien qu'une seule page
    }

    public function testHasMorePagesMethod(): void
    {
        // Il y a 15 éléments à afficher par page.
        // Donc si on est sur la page 2, que qu'à paginate() on lui indique (en param) qu'il y 30 (ou moins) élements à paginer, hasMorePages() retournera false.

        $_GET['page'] = 2;

        $pagination = new Pagination();

        $pagination->paginate(31);

        $this->assertTrue($pagination->hasMorePages());

        $pagination->paginate(30);

        $this->assertFalse($pagination->hasMorePages());

        $pagination->paginate(28);

        $this->assertFalse($pagination->hasMorePages());
    }

    public function testIsFirstPage(): void
    {
        // Il y a 15 éléments à afficher par page. Et on simule 28 élements à paginer.
        // Il y a 2 pages. On se positionne sur la page 1.
        $_GET['page'] = 1;

        $pagination = new Pagination();

        $pagination->paginate(28);

        $this->assertTrue($pagination->isFirstPage());

        // Il y a 15 éléments à afficher par page. Et on simule 28 élements à paginer.
        // Il y a 2 pages. On se positionne sur la page 2.
        $_GET['page'] = 2;

        $pagination = new Pagination();

        $pagination->paginate(28);

        $this->assertFalse($pagination->isFirstPage());

        // On simule n'importe quoi dans l'URL (un string).
        // PS (vs Pagination de Laravel) : on simule qu'on a le même comportement que la pagination livré avec Laravel.
        $_GET['page'] = 'rr';

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertTrue($pagination->isFirstPage()); // par défaut, la pagination nous met bien sur la 1ère page

        // On simule n'importe quoi dans l'URL (un numeric, mais on met un zéro).
        // PS (vs Pagination de Laravel) : on simule qu'on a le même comportement que la pagination livré avec Laravel.
        $_GET['page'] = 0;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertTrue($pagination->isFirstPage()); // par défaut, la pagination nous met bien sur la 1ère page
    }

    public function testIsLastPage(): void
    {
        // Il y a 15 éléments à afficher par page. Et on simule 28 élements à paginer.
        // Il y a 2 pages. On se positionne sur la page 1.
        $_GET['page'] = 1;

        $pagination = new Pagination();

        $pagination->paginate(28);

        $this->assertFalse($pagination->isLastPage());

        // Il y a 15 éléments à afficher par page. Et on simule 28 élements à paginer.
        // Il y a 2 pages. On se positionne sur la page 2.
        $_GET['page'] = 2;

        $pagination = new Pagination();

        $pagination->paginate(28);

        $this->assertTrue($pagination->isLastPage());

        // On simule qu'on se positionne sur une page d'après la dernière page (donc on simule qu'on est une page qui n'existe pas).
        // Il existe que 7 pages, et on se positionne sur la 9ème.
        // PS (vs Pagination de Laravel) : ici isLastPage() n'a pas le même comportement que la pagination livré avec Laravel.
        // La pagination livré avec Laravel, quand on est sur une page d'après la dernière page, "isLastPage()" retorune true.
        $_GET['page'] = 9;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertFalse($pagination->isLastPage()); // nous ne sommes pas sur la dernière page (nous somme sur une page d'après, donc une page qui n'existe pas)
    }

    public function testIsPage(): void
    {
        $_GET['page'] = 4;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertTrue($pagination->isPage(4));
        $this->assertFalse($pagination->isPage(5));

        // On simule qu'on se positionne sur une page d'après la dernière page (donc on simule qu'on est une page qui n'existe pas).
        $_GET['page'] = 8;

        $pagination = new Pagination();

        $pagination->paginate(100);
        $this->assertSame(7, $pagination->getNbPages()); // 100/15 = 6.66666666667 = il y a 7 pages
        $this->assertTrue($pagination->isPage(1)); // ici par "sécurité" ça vaut bien true

        // On simule qu'on se positionne que $_GET['page'] n'existe pas.
        $_GET['page'] = null;

        $pagination = new Pagination();

        $pagination->paginate(100);
        $this->assertSame(7, $pagination->getNbPages()); // 100/15 = 6.66666666667 = il y a 7 pages
        $this->assertTrue($pagination->isPage(1)); // ici par "sécurité" ça vaut bien true
    }

    public function testGetPreviousPageUrl(): void
    {
        $_GET['page'] = 4;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $ex = explode('?page=', $pagination->getPreviousPageUrl());
        $this->assertSame('3', $ex[1]); // page courante - (moins) 1 = 3

        // Si on se positionne sur la 1ère page, sera null.
        $_GET['page'] = 1;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertTrue($pagination->isFirstPage());
        $this->assertTrue($pagination->getPreviousPageUrl() === null);
    }

    public function testGetNextPageUrl(): void
    {
        // On simule qu'on se positionne sur la dernière page.
        $_GET['page'] = 4;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $ex = explode('?page=', $pagination->getNextPageUrl());
        $this->assertSame('5', $ex[1]); // page courante + (plus) 1 = 5

        // Si on se positionne sur la dernière page, sera null.
        $_GET['page'] = 7;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertTrue($pagination->isLastPage()); // on est bien sur la dernière page
        $this->assertTrue($pagination->getNextPageUrl() === null);

        // On simule qu'on se positionne sur une page d'après la dernière page (donc on simule qu'on est une page qui n'existe pas).
        // Il existe que 7 pages, et on se positionne sur la 9ème.
        // PS (vs Pagination de Laravel) : on simule qu'on a le même comportement que la pagination livré avec Laravel.
        $_GET['page'] = 9;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertFalse($pagination->isLastPage()); // nous ne sommes pas sur la dernière page (nous somme sur une page d'après, donc une page qui n'existe pas)
        $this->assertTrue($pagination->getNextPageUrl() === null);

        // On simule n'importe quoi dans l'URL (un string).
        // PS (vs Pagination de Laravel) : on simule qu'on a le même comportement que la pagination livré avec Laravel.
        $_GET['page'] = 'rr';

        $pagination = new Pagination();

        $pagination->paginate(100);

        $ex = explode('?page=', $pagination->getNextPageUrl());
        $this->assertSame('2', $ex[1]); // page par défaut (page 1) + (plus) 1 = 2
    }

    public function testGetFirstPageUrl(): void
    {
        $_GET['page'] = 4;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $ex = explode('?page=', $pagination->getFirstPageUrl());
        $this->assertSame('1', $ex[1]);
    }

    public function testGetLastPageUrl(): void
    {
        $_GET['page'] = 4;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $ex = explode('?page=', $pagination->getLastPageUrl());
        $this->assertSame('7', $ex[1]); // il y a 7 pages
    }

    public function testGetUrl(): void
    {
        $_GET['page'] = 4;

        $pagination = new Pagination();

        $pagination->paginate(100);

        $ex = explode('?page=', $pagination->getUrl(2));
        $this->assertSame('2', $ex[1]);
    }
}
