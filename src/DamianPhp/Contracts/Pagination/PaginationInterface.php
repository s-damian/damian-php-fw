<?php

namespace DamianPhp\Contracts\Pagination;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
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

    /**
     * @return null|int - OFFSET.
     */
    public function getOffset(): ?int;

    /**
     * @return null|int - LIMIT.
     */
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

    public function getGetPP(): null|int|string;

    public function getPageStart(): int;

    public function getPageEnd(): int;

    public function getNumberLinks(): int;

    public function getCssClassP(): string;

    public function getCssClassLinkActive(): string;

    public function getCssIdPP(): string;

    public function getArrayOptionsSelect(): array;

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
     * Rendre le rendu de la pagination au format HTML.
     *
     * @param array|string|null $ifIssetGet - Si il y a déjà des GET dans l'URL, les cumuler avec les liens.
     */
    public function render(array|string $ifIssetGet = null): string;

    /**
     * Rendre le rendu du per page au format HTML.
     *
     * @param array $options
     * - $options['action'] string : Pour l'action du form.
     */
    public function perPage(array $options = []): string;
}
