<?php

namespace DamianPhp\Database\Relations;

use DamianPhp\Contracts\Database\BaseModelInterface;

/**
 * Pour relations : "plusieurs à plusieurs".
 * Fonctionne avec 3 tables (Table du Model instancié + Table de jonction + Table à joindre).
 *
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class BelongsToMany
{
    private BaseModelInterface $model;

    /**
     * Model joint.
     */
    private string $related;

    /**
     * Table de jonction.
     */
    private string $junctionTable;

    /**
     * Clé étrangère de la table de jonction qui fait référence au Model.
     */
    private string $firstKey;

    /**
     * Clé étrangère de la table de jonction qui fait référence au Model joint.
     */
    private string $secondKey;

    /**
     * @param string $related - Model joint.
     * @param string $junctionTable - Table de jonction.
     * @param string $firstKey - Clé étrangère de la table de jonction qui fait référence au Model instancié.
     * @param string $secondKey - Clé étrangère de la table de jonction qui fait référence au Model joint.
     */
    public function __construct(BaseModelInterface $model, string $related, string $junctionTable, string $firstKey, string $secondKey)
    {
        $this->model = $model;
        $this->related = $related;
        $this->junctionTable = $junctionTable;
        $this->firstKey = $firstKey;
        $this->secondKey = $secondKey;
    }

    public function __invoke(): array
    {
        return $this->get();
    }

    /**
     * @return array - Tableaux d'objets hydratés du Model joint.
     */
    private function get(): array
    {
        $relatedInstantiate = new $this->related();

        return $relatedInstantiate->select($relatedInstantiate->getDbTable().'.*')
            ->join(
                $this->junctionTable,
                $relatedInstantiate->getDbTable().'.id',
                '=',
                $this->junctionTable.'.'.$this->secondKey
            )
            ->where($this->junctionTable.'.'.$this->firstKey, '=', $this->model->id)
            ->findAll();
    }
}
