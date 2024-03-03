<?php

declare(strict_types=1);

namespace DamianPhp\Contracts\Pagination;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface PaginationInterface
{
    public function __construct(array $options = []);

    /**
     * Active la pagination.
     *
     * @param int $count - Nombre d'éléments à paginer.
     */
    public function paginate(int $count): void;

    public function getOffset(): ?int;

    public function getLimit(): ?int;

    /**
     * @return int - Nombre total d'éléments sur lesquels on pagine.
     */
    public function getCount(): int;

    /**
     * @return int - Nombre d'éléments sur la page en cours.
     */
    public function getCountOnCurrentPage(): int;

    /**
     * Pour retourner l'indexation du premier élément sur la page en cours
     * Utile pour par exemple afficher : élement "nb start" à ...
     */
    public function getFrom(): int;

    /**
     * Pour retourner l'indexation du deriner élément sur la page en cours
     * Utile pour par exemple afficher : élement ... à "nb end".
     */
    public function getTo(): int;

    /**
     * @return int - Page en cours.
     */
    public function getCurrentPage(): int;

    /**
     * @return int - Nombre de pages.
     */
    public function getNbPages(): int;

    /**
     * @return null|int - Le nombre d'éléments affichés par page.
     */
    public function getPerPage(): ?int;

    /**
     * @return null|int - Le nombre d'éléments affichés par page par defaut.
     */
    public function getDefaultPerPage(): ?int;

    /**
     * @return bool - True s'il y a suffisamment d'éléments à diviser en plusieurs pages.
     */
    public function hasPages(): bool;

    /**
     * @return bool - True si il reste des pages après celle en cours.
     */
    public function hasMorePages(): bool;

    /**
     * @return bool - True si on est sur la première page.
     */
    public function isFirstPage(): bool;

    /**
     * @return bool - True si on est sur la dernière page.
     */
    public function isLastPage(): bool;

    /**
     * @return bool - True si on est sur un numéro de page donné.
     */
    public function isPage(int $nb): bool;

    /**
     * Obtenir l'URL de la page précédente.
     * Renvoie null si nous sommes sur la première page.
     */
    public function getPreviousPageUrl(): ?string;

    /**
     * Obtenir l'URL de la page suivante.
     * Renvoie null si nous sommes sur la dernière page.
     */
    public function getNextPageUrl(): ?string;

    /**
     * Obtenir l'URL de la première page.
     */
    public function getFirstPageUrl(): string;

    /**
     * Obtenir l'URL de la dernière page.
     */
    public function getLastPageUrl(): string;

    /**
     * Obtenir l'URL d'un numéro de page donné.
     */
    public function getUrl(int $nb): string;

    public function getGetPP(): null|int|string;

    public function getPageStart(): int;

    public function getPageEnd(): int;

    public function getNumberLinks(): int;

    public function getCssClassP(): string;

    public function getCssClassLinkActive(): string;

    public function getCssIdPP(): string;

    public function getArrayOptionsSelect(): array;

    /**
     * Rendre le rendu de la pagination au format HTML.
     */
    public function render(): string;

    /**
     * Rendre le rendu du per page au format HTML.
     *
     * @param array $options
     * - $options['action'] string : Pour l'action du form.
     */
    public function perPageForm(array $options = []): string;
}
