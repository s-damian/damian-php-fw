<?php

namespace DamianPhp\Database;

use DamianPhp\Contracts\Database\BaseModelInterface;

/**
 * Pour retourner une collection d'objets d'un modèle hydraté.
 *
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Collection
{
    private BaseModelInterface $model;

    private mixed $sqlQuery;

    private array $items = [];

    public function __construct(BaseModelInterface $model, mixed $sqlQuery)
    {
        $this->model = $model;
        $this->sqlQuery = $sqlQuery;

        $this->setPropertiesAndAddCollectionToItems();
    }

    public function __invoke(): array
    {
        return $this->items;
    }

    /**
     * Pour hydrater des objets à partir d'une requete SQL qui récupère plusieures lignes.
     */
    private function setPropertiesAndAddCollectionToItems(): void
    {
        foreach ($this->sqlQuery as $ligneQuery) {
            $object = new $this->model();

            foreach ($ligneQuery as $property => $value) {
                if (property_exists($this->model, $property)) {
                    $object->$property = $value;
                }
            }

            $this->items[] = $object;
        }
    }
}
