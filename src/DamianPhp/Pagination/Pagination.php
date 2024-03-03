<?php

declare(strict_types=1);

namespace DamianPhp\Pagination;

use DamianPhp\Http\Request\Request;
use DamianPhp\Contracts\Pagination\PaginationInterface;

/**
 * Classe client.
 * Pour générer une pagination.
 *
 * # Fonctionnement de ce package :
 * Pour générer le rendu, la classe "Pagination" fait appelle à la classe "HtmlRenderer" qui est une classe enfant de "RendererGenerator".
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Pagination implements PaginationInterface
{
    public const PAGE_NAME = 'page';

    public const PER_PAGE_NAME = 'pp';

    public const PER_PAGE_OPTION_ALL = 'all';

    public const REGEX_INTEGER = '/^[0-9]+$/';

    private Request $request;

    private ?int $getP = null;

    /**
     * @var null|int|string - If 'all' in URL, it will be a string.
     */
    private null|int|string $getPP = null;

    /**
     * Nombre d'elements par page.
     */
    private ?int $perPage = null;

    /**
     * Nombre de pages total.
     */
    private int $nbPages;

    /**
     * Page en cours.
     */
    private int $currentPage;

    /**
     * Page de départ.
     */
    private int $pageStart;

    /**
     * Page de fin.
     */
    private int $pageEnd;

    /**
     * Les options du <select>.
     */
    private array $arrayOptionsSelect = [];

    /**
     * OFFSET - A partir d'où on débute le LIMIT.
     */
    private ?int $offset;

    /**
     * LIMIT - Nombre d'éléments à récupérer (sur la page en cours).
     */
    private ?int $limit;

    /**
     * Nombre total d'éléments sur lesquels paginer.
     */
    private int $count;

    /**
     * Nombre d'éléments sur lesquels on effectue la pagination.
     */
    private int $defaultPerPage;

    /**
     * Nombre de liens aux cotés de la page courante.
     */
    private int $numberLinks;

    /**
     * Class CSS par defaut de la pagination.
     */
    private string $cssClassP;

    /**
     * Classe CSS du lien actif de la pagination.
     */
    private string $cssClassLinkActive;

    /**
     * ID CSS par defaut du par page de la pagination.
     */
    private string $cssIdPP;

    private HtmlRenderer $htmlRenderer;

    public function __construct(array $options = [])
    {
        $this->request = new Request();

        if ($this->request->getGet()->has(self::PAGE_NAME) && is_numeric($this->request->getGet()->get(self::PAGE_NAME))) {
            $this->getP = (int) $this->request->getGet()->get(self::PAGE_NAME);
        }

        if ($this->request->getGet()->has(self::PER_PAGE_NAME)) {
            if ($this->request->getGet()->get(self::PER_PAGE_NAME) === self::PER_PAGE_OPTION_ALL) {
                $this->getPP = $this->request->getGet()->get(self::PER_PAGE_NAME);
            } else {
                $this->getPP = is_numeric($this->request->getGet()->get(self::PER_PAGE_NAME)) ? (int) $this->request->getGet()->get(self::PER_PAGE_NAME) : null;
            }
        }

        $this->extractOptions($options);

        $this->htmlRenderer = new HtmlRenderer($this);
    }

    private function extractOptions(array $options = []): void
    {
        $this->defaultPerPage = isset($options['pp']) && is_integer($options['pp'])
            ? $options['pp']
            : 15;

        $this->numberLinks = isset($options['number_links']) && is_integer($options['number_links'])
            ? $options['number_links']
            : 5;

        $this->arrayOptionsSelect = isset($options['options_select']) && is_array($options['options_select'])
            ? $options['options_select'] :
            [15, 30, 50, 100, 200, 300];

        $this->cssClassP = isset($options['css_class_p']) && is_string($options['css_class_p'])
            ? $options['css_class_p']
            : 'pagination';

        $this->cssClassLinkActive = isset($options['css_class_link_active']) && is_string($options['css_class_link_active']) ?
            $options['css_class_link_active']
            : 'active';

        $this->cssIdPP = isset($options['css_id_pp']) && is_string($options['css_id_pp'])
            ? $options['css_id_pp']
            : 'per-page-form';
    }

    /**
     * Active la pagination.
     *
     * @param int $count - Nombre d'éléments à paginer.
     */
    public function paginate(int $count): void
    {
        $this->count = $count;

        $this->treatmentPerPage();

        if ($this->perPage !== null) { // si pas sur "Tous"
            $this->nbPages = (int) ceil($this->count / $this->perPage);
        } else {
            $this->nbPages = 1;
        }

        // si $this->getP existe, et si $this->getP est > à 0, et si $this->getP <= au nombre de page, et si $this->getP est bien un chiffre...
        if ($this->getP !== null && $this->getP > 0 && $this->getP <= $this->nbPages && preg_match(self::REGEX_INTEGER, (string) $this->getP)) {
            $this->currentPage = $this->getP; // c'est la page qui change
        } else {
            $this->currentPage = 1; // par defaut la page en cours est la 1è page
        }

        $this->setLimitAndSetOffset();
    }

    /**
     * Traitement du nombre d'éléments par page (pour <select>)
     */
    private function treatmentPerPage(): void
    {
        if ($this->getPP !== null && (preg_match(self::REGEX_INTEGER, (string) $this->getPP) || $this->getPP === self::PER_PAGE_OPTION_ALL)) {
            if (in_array($this->getPP, $this->arrayOptionsSelect)) {
                if ($this->getPP === self::PER_PAGE_OPTION_ALL) { // pour si clic sur "Tous"
                    $this->perPage = null;
                    $this->getP = 1;
                } else {
                    $this->perPage = (int) $this->getPP;
                }
            } else {
                $this->perPage = $this->defaultPerPage; // sécuritée, si pas dans array
            }
        } else {
            $this->perPage = $this->defaultPerPage; // si pas de $this->getPP, nb d'elements par page de par defaut (10 elements par ex.)
        }
    }

    /**
     * Pour setter le LIMIT et le OFFSET.
     */
    private function setLimitAndSetOffset(): void
    {
        if ($this->perPage === null) { // pour "Tous"
            $this->offset = null;
            $this->limit = null;
        } else {
            $this->offset = ($this->currentPage - 1) * $this->perPage;
            $this->limit = $this->perPage;
        }
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Retourner le nombre total d'éléments sur lesquels on pagine.
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Retourner le nombre d'éléments sur la page en cours.
     */
    public function getCountOnCurrentPage(): int
    {
        if ($this->count < $this->perPage || $this->perPage === null) {
            return $this->count;
        } else {
            if ($this->hasMorePages()) {
                return $this->perPage;
            } else {
                return $this->getCountOnLastPage();
            }
        }
    }

    /**
     * Pour retourner l'indexation du premier élément sur la page en cours
     * Utile pour par exemple afficher : élement "nb start" à ...
     */
    public function getFrom(): int
    {
        return $this->getFromTo()['from'];
    }

    /**
     * Pour retourner l'indexation du deriner élément sur la page en cours
     * Utile pour par exemple afficher : élement ... à "nb end".
     */
    public function getTo(): int
    {
        return $this->getFromTo()['to'];
    }

    /**
     * Pour retourner l'indexation du premier élément et l'indexation du deriner élément sur la page en cours
     * Utile pour par exemple afficher : élement "nb start" à "nb end" sur cette page.
     *
     * @return array - Array associatif
     *    'from' => nb start
     *    'to' => nb end
     */
    private function getFromTo(): array
    {
        if ($this->count < $this->perPage || $this->perPage === null) {
            $start = 1;
            $end = $this->count;
        } else {
            if ($this->hasMorePages()) {
                $end = $this->perPage * $this->currentPage;
                $start = ($end - $this->perPage) + 1;
            } else {
                $endTest = $this->perPage * $this->currentPage;
                $start = ($endTest - $this->perPage) + 1;

                $end = $start + $this->getCountOnLastPage();
            }
        }

        return ['from' => $start, 'to' => $end];
    }

    /**
     * Retourner le nombre d'éléments sur la dernière page.
     */
    private function getCountOnLastPage(): int
    {
        $a = $this->perPage * $this->nbPages;
        $b = $a - $this->count;
        $c = $this->perPage - $b;

        return $c;
    }

    /**
     * Retourner la page en cours.
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Retourner le nombre de pages.
     */
    public function getNbPages(): int
    {
        return $this->nbPages;
    }

    /**
     * Retourner le nombre d'éléments affichés par page.
     */
    public function getPerPage(): ?int
    {
        return $this->perPage ?? null;
    }

    /**
     * Retourner le nombre d'éléments affichés par page par defaut.
     */
    public function getDefaultPerPage(): ?int
    {
        return $this->defaultPerPage ?? null;
    }

    /**
     * Retourner true s'il y a suffisamment d'éléments à diviser en plusieurs pages.
     */
    public function hasPages(): bool
    {
        return $this->count > $this->perPage;
    }

    /**
     * Retourner true si il reste des pages après celle en cours.
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->nbPages;
    }

    /**
     * Retourner true si on est sur la première page.
     */
    public function isFirstPage(): bool
    {
        if ($this->request->getGet()->has(self::PAGE_NAME)) {
            return $this->isPage(1);
        }

        return true;
    }

    /**
     * Retourner true si on est sur la dernière page.
     */
    public function isLastPage(): bool
    {
        return $this->currentPage === $this->nbPages;
    }

    /**
     * Retourner true si on est sur un numéro de page donné.
     */
    public function isPage(int $nb): bool
    {
        return $this->currentPage === $nb;
    }

    /**
     * Obtenir l'URL de la page précédente.
     * Renvoie null si nous sommes sur la première page.
     */
    public function getPreviousPageUrl(): ?string
    {
        if (! $this->isFirstPage()) {
            return $this->request->getFullUrlWithQuery([self::PAGE_NAME => ($this->currentPage - 1)]);
        }

        return null;
    }

    /**
     * Obtenir l'URL de la page suivante.
     * Renvoie null si nous sommes sur la dernière page.
     */
    public function getNextPageUrl(): ?string
    {
        if ($this->getP < $this->nbPages) {
            return $this->request->getFullUrlWithQuery([self::PAGE_NAME => ($this->currentPage + 1)]);
        }

        return null;
    }

    /**
     * Obtenir l'URL de la première page.
     */
    public function getFirstPageUrl(): string
    {
        return $this->request->getFullUrlWithQuery([self::PAGE_NAME => 1]);
    }

    /**
     * Obtenir l'URL de la dernière page.
     */
    public function getLastPageUrl(): string
    {
        return $this->request->getFullUrlWithQuery([self::PAGE_NAME => $this->nbPages]);
    }

    /**
     * Obtenir l'URL d'un numéro de page donné.
     */
    public function getUrl(int $nb): string
    {
        return $this->request->getFullUrlWithQuery([self::PAGE_NAME => $nb]);
    }

    public function getGetPP(): null|int|string
    {
        return $this->getPP;
    }

    public function getPageStart(): int
    {
        return $this->pageStart;
    }

    public function getPageEnd(): int
    {
        return $this->pageEnd;
    }

    public function getNumberLinks(): int
    {
        return $this->numberLinks;
    }

    public function getCssClassP(): string
    {
        return $this->cssClassP;
    }

    public function getCssClassLinkActive(): string
    {
        return $this->cssClassLinkActive;
    }

    public function getCssIdPP(): string
    {
        return $this->cssIdPP;
    }

    public function getArrayOptionsSelect(): array
    {
        return $this->arrayOptionsSelect;
    }

    /**
     * Rendre le rendu de la pagination au format HTML.
     */
    public function render(): string
    {
        $this->setPageStart()->setPageEnd();

        return $this->htmlRenderer->render();
    }

    /**
     * "Limiter le début". pageStart, les éventuels liens cliquables qui seront après la page en cours.
     */
    private function setPageStart(): self
    {
        $firstPage = $this->currentPage - $this->numberLinks;

        // si première page est > ou = à la page 1
        // la page départ (à afficher en lien) est la page en cours -4 (afficher 4 liens pour aller aux pages précédantes)
        // si non, par defaut, la page départ est la page 1
        if ($firstPage >= 1) {
            $this->pageStart = $firstPage;
        } else {
            $this->pageStart = 1;
        }

        return $this;
    }

    /**
     * "Limiter la fin". pageEnd, les éventuels liens cliquables qui seront avant la page en cours.
     */
    private function setPageEnd()
    {
        $lastPage = $this->currentPage + $this->numberLinks;

        // si dernière page est < ou = au nombre de pages
        // la page fin (à afficher en lien) est la page en cours +4 (afficher 4 liens pour aller aux pages suivantes)
        // si non, la page fin est la dernière page du nombre de pages
        if ($lastPage <= $this->nbPages) {
            $this->pageEnd = $lastPage;
        } else {
            $this->pageEnd = $this->nbPages;
        }
    }

    /**
     * Rendre le rendu du per page au format HTML.
     *
     * @param array $options
     * - $options['action'] string : Pour l'action du form.
     */
    public function perPageForm(array $options = []): string
    {
        return $this->htmlRenderer->perPageForm($this->request, $options);
    }
}
