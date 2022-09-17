<?php

namespace DamianPhp\Database\Relations;

use DamianPhp\Contracts\Database\BaseModelInterface;

/**
 * Pour relations : "un à un", "un à plusieurs inversés".
 * Fonctionne avec 2 tables (Table du Model instancié + Table à joindre).
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class HasOne
{
    private BaseModelInterface $model;

    /**
     * Model joint.
     */
    private string $related;

    /**
     * Clé étrangère de la table du Model.
     */
    private string $foreignKey;

    /**
     * @param string $related - Model joint.
     * @param string $foreignKey - Clé étrangère de la table du Model.
     */
    public function __construct(BaseModelInterface $model, string $related, string $foreignKey)
    {
        $this->model = $model;
        $this->related = $related;
        $this->foreignKey = $foreignKey;
    }

    public function __invoke(): ?object
    {
        return $this->get();
    }

    /**
     * @return object - Object hydraté du Model à joint.
     */
    private function get(): ?object
    {
        $relatedInstantiate = new $this->related();
        $foreignKey = $this->foreignKey;

        return $relatedInstantiate->select($relatedInstantiate->getDbTable().'.*')
            ->where($relatedInstantiate->getDbTable().'.id', '=', $this->model->$foreignKey)
            ->find();
    }
}
