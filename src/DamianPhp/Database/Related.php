<?php

declare(strict_types=1);

namespace DamianPhp\Database;

use PDO;
use DamianPhp\Contracts\Database\BaseModelInterface;

/**
 * Attache / Détache / Sync.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Related
{
    private BaseModelInterface $model;

    private Query $query;

    public function __construct(BaseModelInterface $model, Query $query)
    {
        $this->model = $model;
        $this->query = $query;
    }

    /**
     * Attacher.
     *
     * @param string $junctionTable - Table de jonction qui sert à faire la relation Many to Many.
     */
    public function attach(string $junctionTable, array $data): void
    {
        $data = $this->getData($data);

        // si on veut lui joindre des "rows du Model joint"
        if ($data[3] !== '') {
            // INSERT INTO dans la table de jonction pour joindre à "row" ses "rows du Model joint"
            $this->query->setStart("INSERT INTO ".$junctionTable." (".$data[0].", ".$data[2].") VALUES (:id_a, :id_b)");

            $this->query->prepare();

            $this->query->getSqlQuery()->bindValue(':id_a', $data[1], PDO::PARAM_INT);
            // et ci-dessous, le paramètre "$relatedId" qui va changer en boucle
            /** @var int $relatedId */
            $this->query->getSqlQuery()->bindParam(':id_b', $relatedId, PDO::PARAM_INT);
            foreach ($data[3] as $relatedId) {
                $this->query->execute();
            }
        }
    }

    /**
     * Sync ("détache" et "attache").
     *
     * @param string $junctionTable - Table de jonction qui sert à faire la relation Many to Many.
     */
    public function sync(string $junctionTable, array $data): void
    {
        $data = $this->getData($data);

        // ***** étape 1 : récupérer toutes les jointures déjà existantes pour ce "row" *****
        $relatedModelIds = []; // aura les IDs des "rows du Model joint" qu'on veut joindre (via une case à cocher par exemple).
        // on parcours tous les "rows du Model joint" cochées
        foreach ($data[3] as $column) {
            $relatedModelIds[] = $column;
        }

        $sql = "SELECT ".$junctionTable.".* FROM ".$junctionTable." WHERE ".$data[0]." = ?";

        $query = $this->model->query($sql, [$data[1]]);

        $relatedModelIds_toRemove = []; // aura les ID des "rows du Model joint" à supprimer de la BDD (cases décochées, et qui étaient présentes dans BDD).
        $relatedModelIds_notToInsert = []; // aura les ID des "rows du Model joint" cohées qui en sont pas à insérer de la BDD (car étaient déjà présentes dans BDD).
        // on parcours tous les "rows du Model joint" jointes
        foreach ($query as $ligneQuery) {
            foreach ($ligneQuery as $column => $value) {
                if ($column === $data[2]) {
                    // si "row du Model joint" (qui était présente dans BDD) a été décoché, faut la supprimer de la BDD.
                    if (! in_array($value, $relatedModelIds)) {
                        $relatedModelIds_toRemove[] = $value;
                    }

                    // on lui push tous les "rows du Model joint" déjà joints à ce "row"
                    $relatedModelIds_notToInsert[] = $value;
                }
            }
        }

        $query->closeCursor();
        // ***** /étape 1 : récupérer tous les jointures déjà existantes pour ce "row" *****

        // ***** étape 2 : on supprime les jointures WHERE "rows du Model joint" décochées *****
        if ($relatedModelIds_toRemove !== []) {
            $this->model->whereIn($data[2], $relatedModelIds_toRemove)->delete();
        }
        // ***** /étape 2 : on supprime les jointures WHERE "rows du Model joint" décochées *****

        $this->query->resetSql();

        // ***** étape 3 : on insère les nouvelles jointures WHERE "rows du Model joint" cochées (sans doublon) *****
        $relatedModelIds_toInsert = []; // aura les ID des "rows du Model joint" cohées qui sont à insérer de la BDD (cases cochées, et qui n'étaient pas déjà présentes dans BDD)
        // on parcours tous les "rows du Model joint" cochées
        foreach ($data[3] as $relatedModelId_checked) {
            if (! in_array($relatedModelId_checked, $relatedModelIds_notToInsert)) {
                $relatedModelIds_toInsert[] = (int) $relatedModelId_checked;
            }
        }

        if ($relatedModelIds_toInsert !== []) {
            $dataAfterFilters = [
                0 => $data[0],
                1 => $data[1],
                2 => $data[2],
                3 => $relatedModelIds_toInsert,
            ];

            $this->attach($junctionTable, $dataAfterFilters);
        }
        // ***** /étape 3 : on insère les nouvelles jointures WHERE "rows du Model joint" cochées (sans doublon) *****
    }

    /**
     * @return array
     * - $data[0] (string) : Colonne (clé étrangère) de la table de jonction qui fait référence au Model.
     * - $data[1] (int)    : Colonne "id" du Model sur lequel on fait la jointure avec le Model joint.
     * - $data[2] (string) : Colonne (clé étrangère) de la table de jonction qui fait référence au Model joint.
     * - $data[3] (array)  : Les id des rows du Model joint qu'on attache au Model.
     */
    private function getData(array $data): array
    {
        if (count($data) === 2) { // pour si la $data passé à attach() n'est pas déjà "rangé".
            $dataToReturn = [];

            foreach ($data as $key => $value) {
                // condition pour ne pas être obligé de "respecter" l'ordre dans controller.
                // si $value === '', ça voudra dire qu'il n'y a pas eu de case cochée.
                if (is_array($value) || $value === '') {
                    $dataToReturn[2] = $key;
                    $dataToReturn[3] = $value === '' ? [] : $value;
                } else {
                    $dataToReturn[0] = $key;
                    $dataToReturn[1] = $value;
                }
            }

            return $dataToReturn;
        }

        return $data;
    }
}
