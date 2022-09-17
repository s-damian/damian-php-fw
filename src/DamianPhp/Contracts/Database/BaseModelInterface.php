<?php

namespace DamianPhp\Contracts\Database;

use DamianPhp\Pagination\Pagination;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface BaseModelInterface
{
    public function __construct();

    /**
     * @return bool|BaseModel|mixed
     */
    public function __call(string $method, array $arguments): mixed;

    public function getConnector(): string;

    public function getDriver(): string;

    /**
     * @return static - Instance du Model.
     */
    public static function load(): static;

    /**
     * @return string - Nom de la table avec préfix.
     */
    public function getDbTable(): string;

    /**
     * @param string $related - Model joint.
     * @param string $foreignKey - Clé étrangère de la table du Model.
     * @return object - Object hydraté du Model à joint.
     */
    public function hasOne(string $related, string $foreignKey): ?object;

    /**
     * @param string $related - Model joint.
     * @param string $foreignKeyOfTableRelated - Clé étrangère de la table du Model joint.
     * @return object - Object hydraté du Model à joint.
     */
    public function hasMany(string $related, string $foreignKeyOfTableRelated): array;

    /**
     * @param string $related - Model joint.
     * @param string $junctionTable - Table de jonction.
     * @param string $firstKey - Clé étrangère de la table de jonction qui fait référence au Model instancié.
     * @param string $secondKey - Clé étrangère de la table de jonction qui fait référence au Model joint.
     * @return array - Tableaux d'objets hydratés du Model joint.
     */
    public function belongsToMany(string $related, string $junctionTable, string $firstKey, string $secondKey): array;

    /**
     * @param array|string $columnsParam - Colonnes à selectionner.
     */
    public function select(array|string $columnsParam): self;

    /**
     * @param string $join - Table de jonction pour les requetes INNER JOIN
     * @param array $on - ON du INNER JOIN
     * @return $this
     */
    public function join(string $join, ...$on): self;

    /**
     * @param string $leftJoin - Table de jonction pour les requetes LEFT JOIN
     * @param array $on - ON du LEFT JOIN
     * @return $this
     */
    public function leftJoin(string $leftJoin, ...$on): self;

    /**
     * @param string $rightJoin - Table de jonction pour les requetes RIGHT JOIN
     * @param array $on - ON du RIGHT JOIN
     * @return $this
     */
    public function rightJoin(string $rightJoin, ...$on): self;

    /**
     * Pour éventuellement ajouter condition(s) à la requete si certaine(s) condition(s) sont true.
     */
    public function when(bool $condition, callable $callable): self;

    /**
     * Pour éventuellement ajouter condition(s) (avec AND si plusieures conditions).
     */
    public function where(array|string $where): self;

    /**
     * Pour ajouter condition(s) (avec OR si plusieures conditions).
     */
    public function orWhere(...$where): self;

    /**
     * Pour ajouter condition(s) avec WHERE IN (et avec AND si plusieures conditions).
     */
    public function whereIn(string $column, array $values): self;

    /**
     * Pour ajouter condition(s) avec WHERE IN (et avec OR si plusieures conditions).
     */
    public function orWhereIn(string $column, array $values): self;

    /**
     * Pour éventuellement ajouter un orderBy avec un order
     *
     * @param string $orderBy - Afficher par.
     * @param string $order - Ordre d'affichage.
     */
    public function orderBy(string $orderBy, string $order = 'ASC'): self;

    /**
     * Pour éventuellement ajouter un LIMIT - Nombre d'éléments à récupérer.
     */
    public function limit(?int $limit): self;

    /**
     * Pour éventuellement ajouter un OFFSET - A partir d'où on débute le LIMIT.
     */
    public function offset(?int $offset): self;

    /**
     * @return $this - Object hydraté, ou erreur HTTP 404.
     */
    public function findOrFail(int $id = null): self;

    /**
     * Pour les requetes SQL qui retournent une seule ligne.
     *
     * @return null|$this - Object hydraté.
     */
    public function find(): ?self;

    /**
     * Pour les requetes SQL qui retournent plusieures lignes.
     *
     * @return array - Un tableaux d'objets hydratés du model.
     */
    public function findAll(): array;

    /**
     * @param int $perPage - Nombre d'éléments par page.
     * @return array - Collection d'objets du Model paginée.
     */
    public function paginate(int $perPage = 10): array;

    /**
     * @return string|Pagination
     */
    public function getPagination(): string|Pagination;

    /**
     * @return int - Nombre d'éléments paginés.
     */
    public function getTotal(): int;

    /**
     * Compter nombre de lignes totales dans une table.
     */
    public function count(string $column = '*'): int;

    /**
     * Calculer la somme des valeurs contenus dans une colonne.
     */
    public function sum(string $column): float|int;

    /**
     * Retourner la valeur maximum d'une colonne.
     */
    public function max(string $column): float|int;

    /**
     * Pour assigner en masse.
     */
    public function fill(array $data): void;

    /**
     * Insérer ou modifier un élément avec le design pattern Active Record.
     */
    public function save(): void;

    /**
     * Pour les requetes SQL INSERT INTO - Insertion d'une nouvelle ligne.
     *
     * @param array $data - Colonnes où faire le INSERT, et valeurs à insérer.
     */
    public function create(array $data): void;

    /**
     * Pour les requetes SQL UPDATE - Modifications sur des lignes existantes.
     *
     * @param array $data - Colonnes où faire le UPDATE, et valeurs à insérer.
     */
    public function update(array $data): void;

    /**
     * @return int - Dernier ID inséré par auto-incrémentation.
     */
    public function getLastInsertId(): int;

    /**
     * Pour les requetes SQL DELETE - Supprimer ligne(s) dans une table.
     */
    public function delete(): void;

    /**
     * Démarre une transaction.
     *
     * @return mixed
     */
    public function beginTransaction(): mixed;

    /**
     * Valide une transaction.
     */
    public function validTransaction(): mixed;

    /**
     * Annule une transaction.
     */
    public function cancelTransaction(): mixed;

    /**
     * @param bool $bool - Mettre true pour si on veut récupérer nombre de lignes affectées.
     */
    public function runRowCount(bool $bool): self;

    /**
     * @return null|int - Nombre de lignes affectées.
     */
    public function getRowCount(): ?int;

    /**
     * @return string - Date actuelle.
     */
    public function getNow(): string;

    /**
     * Attacher.
     *
     * @param string $junctionTable - Table de jonction qui sert à faire la relation Many to Many.
     * @param array $data
     * - $data[0] string : Champ 'post_id' dans table de jonction.
     * - $data[1] string : $variable, id du post (à d'abbord supprimer anciènne jointures dans table de jonction), et à ensuite insérer dans table de jonction.
     * - $data[2] string : Champ 'category_id' dans table de jonction.
     * - $data[3] array : $variable, list des catégorie(s) cochée(s) pour faire jointure avec id_post dans table de jonction.
     */
    public function attach(string $junctionTable, array $data): void;

    /**
     * Détache et attache.
     *
     * @param string $junctionTable - Table de jonction qui sert à faire la relation Many to Many.
     */
    public function sync(string $junctionTable, array $data): void;
}
