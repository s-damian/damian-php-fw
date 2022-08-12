<?php

namespace DamianPhp\Database;

use DamianPhp\Pagination\Pagination;
use DamianPhp\Contracts\Database\BaseModelInterface;

/**
 * Pour retourner des collections paginées.
 * Fait 2 requêtes SQL :
 * Fait d'abord une requête SQL COUNT, et fait ensuite une requête SQL qui récupère plusieures lignes.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Paginated
{
    private BaseModelInterface $model;

    private Pagination $pagination;

    /**
     * @var int - Nombre d'éléments paginés.
     */
    private int $total = 0;

    public function __construct(BaseModelInterface $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $perPage - Nombre d'éléments par page.
     * @return array - Collection d'objets du Model paginé.
     */
    public function paginate(int $perPage): array
    {
        $this->total = $this->model->count();

        $this->pagination = new Pagination(['pp' => $perPage]);

        $this->pagination->paginate($this->total);

        return $this->model->limit($this->pagination->getLimit())
            ->offset($this->pagination->getOffset())
            ->findAll();
    }

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    /**
     * @return int - Nombre d'éléments paginés.
     */
    public function getTotal(): int
    {
        return $this->total;
    }
}
