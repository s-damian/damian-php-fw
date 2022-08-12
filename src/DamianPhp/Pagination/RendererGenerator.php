<?php

namespace DamianPhp\Pagination;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Str;
use DamianPhp\Support\Facades\Server;
use DamianPhp\Contracts\Http\Request\RequestInterface;
use DamianPhp\Contracts\Pagination\PaginationInterface;

/**
 * Rendu de la pagination.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
abstract class RendererGenerator
{    
    protected PaginationInterface $pagination;

    /**
     * Sera array ou string - pour cumuler les éventuels GET avec le render et le perPage.
     */
    private null|array|string $accumulateIfHasQueryParams = null;

    final public function __construct(PaginationInterface $pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * Pour afficher la pagination.
     *
     * @param null|array|string $ifIssetGet - Si il y a déjà des GET dans l'URL, les cumuler avec les liens.
     */
    final public function render(array|string $ifIssetGet = null): string
    {
        $html = '';

        $andIfHasQueryString = $this->accumulateIfHasQueryParams($ifIssetGet);

        if ($this->pagination->getGetPP() !== Pagination::PER_PAGE_OPTION_ALL && $this->pagination->getCount() > $this->pagination->getPerPage()) {
            /** @var HtmlRenderer $this */
            $html .= $this->open();

            $html .= $this->previousLink(Str::andIfHasQueryString($andIfHasQueryString));
            $html .= $this->firstLink(Str::andIfHasQueryString($andIfHasQueryString));

            for ($i = $this->pagination->getPageStart(); $i <= $this->pagination->getPageEnd(); $i++) {
                if ($i === $this->pagination->getCurrentPage()) { // si page en cours
                    $html .= $this->paginationActive($i);
                } else { // si pas la page en cours
                    if ($i !== 1 && $i !== $this->pagination->getNbPages()) { // Si pas la première, ni la dernière page
                        $html .= $this->paginationLink($i.Str::andIfHasQueryString($andIfHasQueryString), $i);
                    }
                }
            }

            $html .= $this->lastLink(Str::andIfHasQueryString($andIfHasQueryString));
            $html .= $this->nextLink(Str::andIfHasQueryString($andIfHasQueryString));

            $html .= $this->close();
        }

        return $html;
    }

    /**
     * Pour cumuler les éventuel GET.
     *
     * @param null|array|string - $ifIssetGet
     * @return array - les éventuels paramètres déjàs en GET à cumuler avec pagination.
     */
    private function accumulateIfHasQueryParams(array|string $ifIssetGet = null): array
    {
        if ($ifIssetGet !== null) {
            $this->accumulateIfHasQueryParams = $ifIssetGet; // pour perPage

            $andIfHasQueryString = [Pagination::PER_PAGE_NAME]; // pour render
            if (is_array($this->accumulateIfHasQueryParams)) {
                foreach ($this->accumulateIfHasQueryParams as $oneGet) {
                    $andIfHasQueryString[] = $oneGet;
                }
            } else {
                $andIfHasQueryString[] = $ifIssetGet;
            }

        } else {
            $andIfHasQueryString = [Pagination::PER_PAGE_NAME];
        }

        return $andIfHasQueryString;
    }

    /**
     * Pour choisir nombre d'éléments à afficher par page.
     *
     * @param array $options
     * - $options['action'] (string) : Pour l'action du form.
     */
    final public function perPage(RequestInterface $request, array $options = []): string
    {
        $html = '';

        if ($this->pagination->getCount() > $this->pagination->getDefaultPerPage()) {
            $actionPerPage = isset($options['action']) && is_string($options['action']) ? $options['action'] : Server::getRequestUri();

            /** @var HtmlRenderer $this */
            $onChange = !$request->isAjax() ? $this->perPageOnchange() : '';

            $html .= $this->perPageOpenForm($actionPerPage);
            $html .= $this->perPageLabel();
            $html .= $this->perPageInputHidden();
            $html .= $this->perPageOpenSelect($onChange);   

            foreach ($this->pagination->getArrayOptionsSelect() as $valuePP) {
                /** @var self $this */
                $html .= $this->generateOption($valuePP);
            }

            /** @var HtmlRenderer $this */
            $html .= $this->perPageCloseSelect();

            if ($this->accumulateIfHasQueryParams !== null) {
                $html .= Str::inputHiddenIfHasQueryString($this->accumulateIfHasQueryParams);
            }

            $html .= $this->perPageCloseForm();
        }

        return $html;
    }

    private function generateOption(int|string $valuePP): string
    {
        $html = '';

        $selectedPP = $valuePP === $this->pagination->getGetPP()
            ? 'selected'
            : '';

        $selectedDefault = $this->pagination->getGetPP() === null && $valuePP === $this->pagination->getDefaultPerPage()
            ? 'selected'
            : '';

        /** @var HtmlRenderer $this */
        if (
            $this->pagination->getCount() >= $valuePP &&
            $valuePP !== $this->pagination->getDefaultPerPage() &&
            $valuePP !== Pagination::PER_PAGE_OPTION_ALL
        ) {
            $html .= $this->perPageOption($selectedDefault.$selectedPP, $valuePP);
        } elseif ($valuePP === $this->pagination->getDefaultPerPage() || $valuePP === Pagination::PER_PAGE_OPTION_ALL) { // afficher ces 3 <option> en permanance
            if ($valuePP === Pagination::PER_PAGE_OPTION_ALL) {
                $html .= $this->perPageOption($selectedDefault.$selectedPP, $valuePP, Helper::lang('pagination')[Pagination::PER_PAGE_OPTION_ALL]);
            } else {
                $html .= $this->perPageOption($selectedDefault.$selectedPP, $valuePP);
            }
        }

        return $html;
    }
}
