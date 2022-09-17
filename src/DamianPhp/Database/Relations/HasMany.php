<?php

namespace DamianPhp\Database\Relations;

use DamianPhp\Contracts\Database\BaseModelInterface;

/**
 * Pour relations : "un à plusieurs".
 * Fonctionne avec 2 tables (Table du Model instancié + Table à joindre).
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class HasMany
{
    private BaseModelInterface $model;

    /**
     * Model joint.
     */
    private string $related;

    /**
     * Clé étrangère de la table du Model joint.
     */
    private string $foreignKeyOfTableRelated;

    /**
     * @param string $related - Model joint.
     * @param string $foreignKeyOfTableRelated - Clé étrangère de la table du Model joint.
     */
    public function __construct(BaseModelInterface $model, string $related, string $foreignKeyOfTableRelated)
    {
        $this->model = $model;
        $this->related = $related;
        $this->foreignKeyOfTableRelated = $foreignKeyOfTableRelated;
    }

    public function __invoke(): array
    {
        return $this->get();
    }

    /**
     * @return array - Object hydraté du Model à joint.
     */
    private function get(): array
    {
        $relatedInstantiate = new $this->related();

        return $relatedInstantiate->select($relatedInstantiate->getDbTable().'.*')
            ->where($relatedInstantiate->getDbTable().'.'.$this->foreignKeyOfTableRelated, '=', $this->model->id)
            ->findAll();
    }
}
