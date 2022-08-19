<?php

namespace DamianPhp\Pagination;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Str;
use DamianPhp\Support\Facades\Request;
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

    final public function __construct(PaginationInterface $pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * Pour afficher la pagination.
     */
    final public function render(): string
    {
        $html = '';

        if ($this->pagination->getGetPP() !== Pagination::PER_PAGE_OPTION_ALL && $this->pagination->getCount() > $this->pagination->getPerPage()) {
            /** @var HtmlRenderer $this */
            $html .= $this->open();

            $html .= $this->previousLink();
            $html .= $this->firstLink();

            for ($i = $this->pagination->getPageStart(); $i <= $this->pagination->getPageEnd(); $i++) {
                if ($i === $this->pagination->getCurrentPage()) { // si page en cours
                    $html .= $this->paginationActive($i);
                } else { // si pas la page en cours
                    if ($i !== 1 && $i !== $this->pagination->getNbPages()) { // Si pas la première, ni la dernière page
                        $html .= $this->paginationLink($i);
                    }
                }
            }

            $html .= $this->lastLink();
            $html .= $this->nextLink();

            $html .= $this->close();
        }

        return $html;
    }

    /**
     * Pour choisir nombre d'éléments à afficher par page.
     *
     * @param array $options
     * - $options['action'] string : Pour l'action du form.
     */
    final public function perPageForm(RequestInterface $request, array $options = []): string
    {
        $html = '';

        if ($this->pagination->getCount() > $this->pagination->getDefaultPerPage()) {
            $actionPerPage = isset($options['action']) && is_string($options['action']) ? $options['action'] : Request::getUrlCurrent();

            /** @var HtmlRenderer $this */
            $onChange = ! $request->isAjax() ? $this->perPageOnchange() : '';

            $html .= $this->perPageOpenForm($actionPerPage);
            $html .= $this->perPageLabel();
            $html .= $this->perPageOpenSelect($onChange);   

            foreach ($this->pagination->getArrayOptionsSelect() as $valuePP) {
                /** @var self $this */
                $html .= $this->generateOption($valuePP);
            }

            /** @var HtmlRenderer $this */
            $html .= $this->perPageCloseSelect();
            $html .= Str::inputHiddenIfHasQueryString(['except' => [Pagination::PAGE_NAME, Pagination::PER_PAGE_NAME]]);
            $html .= $this->perPageCloseForm();
        }

        return $html;
    }

    private function generateOption(int|string $valuePP): string
    {
        $html = '';

        if ($this->pagination->getGetPP() !== null) {
            $selected = $valuePP === $this->pagination->getGetPP() ? 'selected' : '';
        } else {
            $selected = $valuePP === $this->pagination->getDefaultPerPage() ? 'selected' : '';
        }

        /** @var HtmlRenderer $this */
        if (
            $this->pagination->getCount() >= $valuePP &&
            $valuePP !== $this->pagination->getDefaultPerPage() &&
            $valuePP !== Pagination::PER_PAGE_OPTION_ALL
        ) {
            $html .= $this->perPageOption($selected, $valuePP);
        } elseif ($valuePP === $this->pagination->getDefaultPerPage() || $valuePP === Pagination::PER_PAGE_OPTION_ALL) { // afficher ces 3 <option> en permanance
            if ($valuePP === Pagination::PER_PAGE_OPTION_ALL) {
                $html .= $this->perPageOption($selected, $valuePP, Helper::lang('pagination')[Pagination::PER_PAGE_OPTION_ALL]);
            } else {
                $html .= $this->perPageOption($selected, (string) $valuePP);
            }
        }

        return $html;
    }
}
