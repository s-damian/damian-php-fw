<?php

declare(strict_types=1);

namespace DamianPhp\Database;

use PDO;
use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Str;
use DamianPhp\Support\Facades\Date;
use DamianPhp\Pagination\Pagination;
use DamianPhp\Database\Relations\HasOne;
use DamianPhp\Database\Relations\HasMany;
use DamianPhp\Database\Relations\BelongsToMany;
use DamianPhp\Contracts\Database\BaseModelInterface;

/**
 * Classe client.
 * Model parent de tous les Models de l'application qui ont besoin de la base de données.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
abstract class BaseModel implements BaseModelInterface
{
    /**
     * Table - Peut être précisée manuellement depuis les Modèles enfants.
     */
    protected string $table;

    /**
     * Les attributs qui sont assignables en masse.
     */
    protected array $fillable = [];

    /**
     * Table avec préfix.
     */
    private string $dbTable;

    private Query $query;

    /**
     * Pour Active Record (INSERT et UPDATE).
     */
    private array $toSave = [];

    /**
     * Pour Active Record (faire UPDATE et non INSERT).
     */
    private bool $firstIsSelected = false;

    /**
     * Pour si oui ou non on veut récupérer le nombre de lignes affectées.
     */
    private bool $runRowCount = false;

    /**
     * Pour récupérer le nombre de lignes affectées.
     */
    private ?int $rowCount = null;

    private Paginated $paginated;

    public function __construct()
    {
        $this->query = new Query($this);

        $this->setDbTable();
    }

    final public function __call(string $method, array $arguments): mixed
    {
        if (mb_substr($method, 0, 3) === 'get' || mb_substr($method, 0, 3) === 'set') {
            return $this->callGetterOrSetter($method, $arguments);
        } elseif (mb_substr($method, 0, 6) === 'findBy') {
            return $this->callFindBy($method, $arguments);
        } elseif (mb_substr($method, 0, 12) === 'findOrFailBy') {
            return $this->callFindOrFailBy($method, $arguments);
        } elseif (mb_substr($method, 0, 9) === 'findAllBy') {
            return $this->callFindAllBy($method, $arguments);
        } elseif (mb_substr($method, 0, 7) === 'countBy') {
            return $this->callCountBy($method, $arguments);
        } else {
            Helper::getExceptionOrLog('Undefined method "'.$method.'".');

            return false;
        }
    }

    /**
     * Modifier table avec prefix
     *
     * - Si le nom de la table est précisée dans Model enfant :
     *   Récupérer manuellement nom de la table avec propriétée "table" dans Model enfant.
     * - Si non :
     *   Récupérer dynamiquement nom de la table selon nom du Model enfant (nom de la table doit être nom de la classe au pluriel).
     */
    private function setDbTable(): void
    {
        if (isset($this->table) && $this->table !== null) {
            $this->dbTable = $this->table;
        } else {
            $classModel = get_called_class();
            $classModelExplode = explode('\\', $classModel);
            $defaultDb = Helper::config('database')['default'];
            $tableSnakePlural = Str::snakePlural(end($classModelExplode));

            if (! isset(Helper::config('database')['connections'][$defaultDb])) {
                Helper::getExceptionOrLog('"'.$defaultDb.'" must be in "connections" in "/config/database.php".');
            }

            $this->dbTable = Helper::config('database')['connections'][$defaultDb]['prefix'].$tableSnakePlural;
        }
    }

    /**
     * Si getter, retourner propriété. si setter, assigner valeur à une propriété.
     */
    private function callGetterOrSetter(string $method, array $arguments)
    {
        $propertyWithUpper = lcfirst(mb_substr($method, 3, mb_strlen($method)));
        $property = Str::convertCamelCaseToSnakeCase($propertyWithUpper);

        if (! property_exists(get_called_class(), $property)) {
            Helper::getExceptionOrLog('Property "'.$property.'" no exist.');

            return false;
        }

        if (mb_substr($method, 0, 3) === 'get') { // getter
            return $this->$property;
        }

        if (mb_substr($method, 0, 3) === 'set') { // setter
            $this->assignProperty($method, $property, $arguments[0]);
        }

        return;
    }

    /**
     * Find WHERE une condition.
     */
    private function callFindBy(string $method, array $arguments): ?self
    {
        $whereByWithUpper = lcfirst(mb_substr($method, 6, mb_strlen($method)));
        $whereBy = Str::convertCamelCaseToSnakeCase($whereByWithUpper);

        return $this->where($whereBy, '=', $arguments[0])->find();
    }

    /**
     * Find ou erreur HTTP 404 WHERE une condition.
     */
    private function callFindOrFailBy(string $method, array $arguments): self
    {
        $whereByWithUpper = lcfirst(mb_substr($method, 12, mb_strlen($method)));
        $whereBy = Str::convertCamelCaseToSnakeCase($whereByWithUpper);

        return $this->where($whereBy, '=', $arguments[0])->findOrFail();
    }

    /**
     * Find all WHERE une condition.
     */
    private function callFindAllBy(string $method, array $arguments): array
    {
        $whereByWithUpper = lcfirst(mb_substr($method, 9, mb_strlen($method)));
        $whereBy = Str::convertCamelCaseToSnakeCase($whereByWithUpper);

        return $this->where($whereBy, '=', $arguments[0])->findAll();
    }

    /**
     * Count WHERE une condition.
     */
    private function callCountBy(string $method, array $arguments): int
    {
        $whereByWithUpper = lcfirst(mb_substr($method, 7, mb_strlen($method)));
        $whereBy = Str::convertCamelCaseToSnakeCase($whereByWithUpper);

        $column = $arguments[1] ?? '*';

        return $this->where($whereBy, '=', $arguments[0])->count($column);
    }

    /**
     * Pour éventuellement utiliser une autre BDD que celle par defaut.
     */
    final protected function setConnector(string $connector): self
    {
        $this->query->setConnector($connector);

        return $this;
    }

    /**
     * Pour éventuellement changer les ID de la connexion à une BDD (avec des ID différents de la config).
     */
    final protected function setIdConnection(array $idConnection): self
    {
        $this->query->setIdConnection($idConnection);

        return $this;
    }

    final protected function getConnection(): PDO
    {
        return $this->query->getConnection();
    }

    final public function getConnector(): string
    {
        return $this->query->getConnector();
    }

    public function getDriver(): string
    {
        return $this->query->getDriver();
    }

    /**
     * @return static - Instance du Model.
     */
    final public static function load(): static
    {
        return new static();
    }

    /**
     * @return string - Nom de la table avec préfix.
     */
    final public function getDbTable(): string
    {
        return $this->dbTable;
    }

    /**
     * @param string $related - Model joint.
     * @param string $foreignKey - Clé étrangère de la table du Model.
     * @return object - Object hydraté du Model à joint.
     */
    final public function hasOne(string $related, string $foreignKey): ?object
    {
        return (new HasOne($this, $related, $foreignKey))();
    }

    /**
     * @param string $related - Model joint.
     * @param string $foreignKeyOfTableRelated - Clé étrangère de la table du Model joint.
     * @return object - Object hydraté du Model à joint.
     */
    final public function hasMany(string $related, string $foreignKeyOfTableRelated): array
    {
        return (new HasMany($this, $related, $foreignKeyOfTableRelated))();
    }

    /**
     * @param string $related - Model joint.
     * @param string $junctionTable - Table de jonction.
     * @param string $firstKey - Clé étrangère de la table de jonction qui fait référence au Model instancié.
     * @param string $secondKey - Clé étrangère de la table de jonction qui fait référence au Model joint.
     * @return array - Tableaux d'objets hydratés du Model joint.
     */
    final public function belongsToMany(string $related, string $junctionTable, string $firstKey, string $secondKey): array
    {
        return (new BelongsToMany($this, $related, $junctionTable, $firstKey, $secondKey))();
    }

    /**
     * @param array|string $columnsParam - Colonnes à selectionner.
     */
    final public function select(array|string $columnsParam): self
    {
        $this->query->select($columnsParam);

        return $this;
    }

    /**
     * @param string $join - Table de jonction pour les requetes INNER JOIN
     * @param array ...$on - ON du INNER JOIN
     */
    final public function join(string $join, ...$on): self
    {
        return $this->addJoin("INNER", $join, $on);
    }

    /**
     * @param string $leftJoin - Table de jonction pour les requetes LEFT JOIN
     * @param array ...$on - ON du LEFT JOIN
     */
    final public function leftJoin(string $leftJoin, ...$on): self
    {
        return $this->addJoin("LEFT OUTER", $leftJoin, $on);
    }

    /**
     * @param string $rightJoin - Table de jonction pour les requetes RIGHT JOIN
     * @param array ...$on - ON du RIGHT JOIN
     */
    final public function rightJoin(string $rightJoin, ...$on): self
    {
        return $this->addJoin("RIGHT OUTER", $rightJoin, $on);
    }

    /**
     * @param string $joinType - Table de jonction pour les requetes JOIN
     * @param string $join - Table de jonction pour les requetes JOIN
     * @param array $on - ON du JOIN
     */
    private function addJoin(string $joinType, string $join, $on): self
    {
        if (count($on) !== 3) {
            Helper::getExceptionOrLog('Method "[x]Join" must have 4 parameters.');
        }

        $this->query->addOn($on)->addJoin($joinType, $join);

        return $this;
    }

    /**
     * Pour éventuellement ajouter condition(s) à la requete si certaine(s) condition(s) sont === à true.
     */
    final public function when(bool $condition, callable $callable): self
    {
        if ($condition === true) {
            return $callable($this);
        }

        return $this;
    }

    /**
     * Pour éventuellement ajouter condition(s) avec WHERE (avec AND si plusieures conditions).
     */
    final public function where(array|string $where): self
    {
        if (is_array($where)) { // si array, on peut utiliser qu'une seule fois cette méthode avec une instance
            $this->query->addWhere($where);
        } else {
            $args = func_get_args();

            $this->query->addWhere($args[0], $args[1], $args[2]);
        }

        return $this;
    }

    /**
     * Pour ajouter condition(s) avec WHERE (avec OR si plusieures conditions).
     */
    final public function orWhere(...$where): self
    {
        $this->query->addOrWhere($where);

        return $this;
    }

    /**
     * Pour ajouter condition(s) avec WHERE IN (et avec AND si plusieures conditions).
     */
    final public function whereIn(string $column, array $values): self
    {
        $this->query->addWhereIn($column, $values);

        return $this;
    }

    /**
     * Pour ajouter condition(s) avec WHERE IN (et avec OR si plusieures conditions).
     */
    final public function orWhereIn(string $column, array $values): self
    {
        $this->query->addOrWhereIn($column, $values);

        return $this;
    }

    /**
     * Pour éventuellement ajouter un orderBy avec un order
     *
     * @param string $orderBy - Afficher par.
     * @param string $order - Ordre d'affichage.
     */
    final public function orderBy(string $orderBy, string $order = 'ASC'): self
    {
        $this->query->addOrderBy($orderBy, $order);

        return $this;
    }

    /**
     * Pour éventuellement ajouter un LIMIT - Nombre d'éléments à récupérer.
     */
    final public function limit(?int $limit): self
    {
        $this->query->setLimit($limit);

        return $this;
    }

    /**
     * Pour éventuellement ajouter un OFFSET - A partir d'où on débute le LIMIT.
     */
    final public function offset(?int $offset): self
    {
        $this->query->setOffset($offset);

        return $this;
    }

    /**
     * @return $this - Object hydraté, ou erreur HTTP 404.
     */
    final public function findOrFail(int $id = null): self
    {
        $result = $this->first($id);

        if (! $result) {
            Helper::getExceptionOrGetError404('No query results for model ['.get_called_class().'].');
        }

        $this->setProperties($result);

        return $this;
    }

    /**
     * Pour les requetes SQL qui retournent une seule ligne.
     *
     * @return null|$this - Object hydraté.
     */
    final public function find(int $id = null): ?self
    {
        $result = $this->first($id);

        if (! $result) {
            return null;
        }

        $this->setProperties($result);

        return $this;
    }

    /**
     * Pour retourner le résultat d'une requete SQL qui retourne une seule ligne.
     */
    private function first(int $id = null): mixed
    {
        $this->firstIsSelected = true; // utile pour active record

        if ($id) {
            $this->where('id', '=', $id);
        }

        $this->query->setStartSelect();

        $this->limit(1);

        $this->query->prepare()->bindWhere()->bindLimit()->execute();

        return $this->query->fetch();
    }

    /**
     * Pour les requetes SQL qui retournent plusieures lignes.
     *
     * @return array - Un tableaux d'objets hydratés du model.
     */
    final public function findAll(): array
    {
        $this->query->setStartSelect();

        $this->query->prepare()->bindWhere()->bindLimit()->execute();

        $this->setRowCount();

        $collection = new Collection($this, $this->query->getSqlQuery());

        $this->query->close();

        return $collection();
    }

    /**
     * @param int $perPage - Nombre d'éléments par page.
     * @return array - Collection d'objets du Model paginée.
     */
    final public function paginate(int $perPage = 10): array
    {
        $this->paginated = new Paginated($this);

        return $this->paginated->paginate($perPage);
    }

    /**
     * @return string|Pagination
     */
    final public function getPagination(): string|Pagination
    {
        if (! $this->paginated instanceof Paginated) {
            Helper::getExceptionOrLog('Method "getPagination()" must be called after method "paginate()".');

            return '';
        }

        return $this->paginated->getPagination();
    }

    /**
     * @return int - Nombre d'éléments paginés.
     */
    final public function getTotal(): int
    {
        if (! $this->paginated instanceof Paginated) {
            Helper::getExceptionOrLog('Method "getTotal()" must be called after method "paginate()".');

            return 0;
        }

        return $this->paginated->getTotal();
    }

    /**
     * Pour les requetes SQL "complexes".
     */
    final public function query(string $sql, array $otpions = []): object
    {
        $query = new Query($this);

        return $query->query($sql, $otpions);
    }

    /**
     * Compter nombre de lignes totales dans une table.
     */
    final public function count(string $column = '*'): int
    {
        return $this->aggregation("COUNT(".$column.")");
    }

    /**
     * Calculer la somme des valeurs contenus dans une colonne.
     */
    final public function sum(string $column): float|int
    {
        return $this->aggregation("SUM(".$column.")");
    }

    /**
     * Retourner la valeur maximum d'une colonne.
     */
    final public function max(string $column): float|int
    {
        return $this->aggregation("MAX(".$column.")");
    }

    private function aggregation(string $aggregation): float|int
    {
        $this->query->setStartAggregation($aggregation);

        $this->query->prepare()->bindWhere()->execute();

        $result = $this->query->fetch();

        $this->query->close();

        return is_string($result->nb) ? (float) $result->nb : $result->nb;
    }

    /**
     * Pour assigner en masse.
     */
    final public function fill(array $data): void
    {
        // si dans les keys de $data il les valeurs de $fillable, assigner
        foreach ($this->fillable as $property) {
            if (array_key_exists($property, $data)) {
                $method = 'set'.ucfirst(Str::convertSnakeCaseToCamelCase($property));

                $this->assignProperty($method, $property, $data[$property]);
            }
        }
    }

    /**
     * Assigner une propriétée.
     */
    private function assignProperty(string $method, string $property, mixed $value): void
    {
        $mutatorMethod = $method.'Attribute';

        // on prépare la valeur à sauvegarder dans la BDD
        if (method_exists($this, $mutatorMethod)) {
            $this->toSave[$property] = $this->$mutatorMethod($value);
        } else {
            $this->toSave[$property] = $value;
        }

        // on assigne la valeur à la propriétée
        $this->$property = $this->toSave[$property];
    }

    /**
     * Insérer ou modifier un élément avec le design pattern Active Record.
     */
    final public function save(): void
    {
        if ($this->firstIsSelected === true) {
            $this->update($this->toSave);
        } else {
            $this->create($this->toSave);
        }
    }

    /**
     * Pour les requetes SQL INSERT INTO - Insertion d'une nouvelle ligne.
     *
     * @param array $data - Colonnes où faire le INSERT, et valeurs à insérer.
     */
    final public function create(array $data): void
    {
        $this->query->setStartInsert($data);

        $this->query->prepare()->bindValuesForInsert($data)->execute();

        $this->setRowCount();

        $this->query->close();
    }

    /**
     * Pour les requetes SQL UPDATE - Modifications sur des lignes existantes.
     *
     * @param array $data - Colonnes où faire le UPDATE, et valeurs à insérer.
     */
    final public function update(array $data): void
    {
        $this->query->setStartUpdate($data);

        $this->query->prepare()->bindSetForUpdate($data)->bindWhere()->bindLimit()->execute();

        $this->setRowCount();

        $this->query->close();
    }

    /**
     * @return int|string|false - Dernier ID inséré par auto-incrémentation.
     */
    final public function getLastInsertId(): int|string|false
    {
        return (int) $this->query->getLastInsertId();
    }

    /**
     * Pour les requetes SQL DELETE - Supprimer ligne(s) dans une table.
     */
    final public function delete(): void
    {
        $this->query->setStartDelete();

        $this->query->prepare()->bindWhere()->bindLimit()->execute();

        $this->setRowCount();

        $this->query->close();
    }

    /**
     * Démarre une transaction.
     */
    final public function beginTransaction(): mixed
    {
        return $this->query->beginTransaction();
    }

    /**
     * Valide une transaction.
     */
    final public function validTransaction(): mixed
    {
        return $this->query->validTransaction();
    }

    /**
     * Annule une transaction.
     */
    final public function cancelTransaction(): mixed
    {
        return $this->query->cancelTransaction();
    }

    /**
     * Pour si on veut récupérer nombre de lignes affectées.
     */
    private function setRowCount(): void
    {
        $this->rowCount = $this->query->setRowCount($this->runRowCount);
    }

    /**
     * @param bool $bool - Mettre true pour si on veut récupérer nombre de lignes affectées.
     */
    final public function runRowCount(bool $bool): self
    {
        $this->runRowCount = $bool;

        return $this;
    }

    /**
     * @return null|int - Nombre de lignes affectées.
     */
    final public function getRowCount(): ?int
    {
        if ($this->rowCount !== null) {
            return $this->rowCount;
        }

        return null;
    }

    /**
     * @return string - Date actuelle.
     */
    final public function getNow(): string
    {
        return Date::getDateTimeFormat();
    }

    /**
     * Hydrater un objet du Model.
     *
     * @param mixed $query - Résultat d'une requete SQL qui récupère une seule ligne.
     * @return $this - Object hydraté.
     */
    final protected function getHydratedObject(mixed $query): self
    {
        $this->setProperties($query);

        return $this;
    }

    /**
     * Pour hydrater un objet à partir d'une requete SQL qui récupère une seule ligne.
     *
     * @param mixed $query - Résultat d'une requete SQL qui récupère une seule ligne.
     */
    private function setProperties(mixed $query): void
    {
        foreach ($query as $property => $value) {
            if (property_exists(get_called_class(), $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * Hydrater des objets du Model.
     *
     * @param mixed $query - Résultat d'une requete SQL qui récupère plusieures lignes.
     * @return array - Collection d'objets.
     */
    final protected function getHydratedObjects($query): array
    {
        $collection = new Collection($this, $query);

        $this->query->close($query);

        return $collection();
    }

    /**
     * Attacher.
     *
     * @param string $junctionTable - Table de jonction qui sert à faire la relation Many to Many.
     * @param array $data - array associatif
     * - 1er élément -
     *   Key (string)  : Colonne (clé étrangère) de la table de jonction qui fait référence au Model.
     *   Value (int)   : Colonne "id" du Model sur lequel on fait la jointure avec le Model joint.
     * - 2ème élément -
     *   Key (string)  : Colonne (clé étrangère) de la table de jonction qui fait référence au Model joint.
     *   Value (array) : Les id des rows du Model joint qu'on attache au Model.
     */
    final public function attach(string $junctionTable, array $data): void
    {
        $related = new Related($this, $this->query);

        $related->attach($junctionTable, $data);
    }

    /**
     * Détache et attache.
     *
     * @param string $junctionTable - Table de jonction qui sert à faire la relation Many to Many.
     */
    final public function sync(string $junctionTable, array $data): void
    {
        $related = new Related($this, $this->query);

        $this->dbTable = $junctionTable;

        $related->sync($junctionTable, $data);
    }
}
