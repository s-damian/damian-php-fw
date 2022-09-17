<?php

namespace DamianPhp\Database;

use PDO;
use DamianPhp\Support\Helper;
use DamianPhp\Contracts\Database\BaseModelInterface;

/**
 * Pour gérer les requètes SQL
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Query
{
    private BaseModelInterface $model;

    /**
     * Pour retourner connexion de la BDD avec PDO.
     */
    private static ?PDO $connection = null;

    /**
     * Pour éventuellement utiliser une autre BDD que celle par defaut.
     */
    private ?string $connector = null;

    /**
     * Utile pour savoir sur quel connecteur (quelle SGBDR) on est connecté pour cette requete SQL.
     */
    private string $driver;

    /**
     * Pour éventuellement changer les ID de la connexion à une BDD (avec des ID différents de la config).
     */
    private array $idConnection = [];

    /**
     * Requete qu'on récupère avec le prepare.
     */
    private mixed $sqlQuery;

    /**
     * Requete SQL.
     */
    private array $sql = [];

    /**
     * Collonne(s) à SELECT.
     */
    private string $columns = '*';

    /**
     * ON du JOIN.
     */
    private array $ons = [];

    /**
     * WHERE - Condition(s) à la requete SQL à ajouter à la requete SQL.
     */
    private array $wheres = [];

    /**
     * WHERE IN - Condition(s) à la requete SQL à ajouter à la requete SQL.
     */
    private array $wheresIn = [];

    /**
     * WHERE - Condition(s) de la requete SQL à traiter avec les bindValue.
     */
    private array $wheresBindValue = [];

    /**
     * Pour le WHERE (peut devenir AND ou OR).
     */
    private string $logicOperator = " WHERE ";

    /**
     * LIMIT - Nombre d'éléments à récupérer.
     */
    private ?int $limit = null;

    /**
     * OFFSET - A partir d'où on débute le LIMIT.
     */
    private ?int $offset = null;

    /**
     * Marqueur de positionnement pour les bindValue.
     */
    private int $positioningMarker = 1;

    /**
     * Opérateurs arithmétiques.
     */
    private const ARITHMETIC_OPERATORS = [
        '+', '-', '*', '/', '%',
    ];

    /**
     * Opérateurs de comparaison.
     */
    private const COMPARAISON_OPERATORS = [
        '=', '<', '>', '<=', '>=', '<>', '!=', '!<', '!>',
        'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'IS NULL', 'IS NOT NULL', 'EXISTS',
    ];

    /**
     * Opérateurs logiques.
     */
    private const LOGIC_OPERATORS = [
        'WHERE', 'AND' ,'OR',
    ];

    public function __construct(BaseModelInterface $model)
    {
        $this->model = $model;

        $this->initSql();
    }

    /**
     * Initialiser la requête SQL.
     */
    private function initSql(): void
    {
        $this->sql['query_type'] = null; // pour savoir pour quel type de requete SQL (SELECT, INSERT, UPDATE, DELETE)
        $this->sql['start'] = null;
        $this->sql['join'] = '';
        $this->sql['where'] = '';
        $this->sql['orderby'] = null;
        $this->sql['limit'] = null;
        $this->sql['offset'] = null;
    }

    /**
     * Pour éventuellement utiliser une autre BDD que celle par defaut.
     */
    public function setConnector(string $connector): void
    {
        $this->connector = $connector;
    }

    /**
     * Pour éventuellement changer les ID de la connexion à une BDD (avec des ID différents de la config).
     */
    public function setIdConnection(array $idConnection): void
    {
        $this->idConnection = $idConnection;
    }

    /**
     * Retourne connexion à une BDD.
     */
    public function getConnection(): PDO
    {
        // autre BDD que celle par defaut. soit avec un autre connector de la config, ou avec autre ID de connexion de la config
        if ($this->connector !== null || count($this->idConnection) > 0) {
            $classNameConnector = $this->getClassNameConnector($this->connector);

            $db = new $classNameConnector($this->connector, $this->idConnection);

            return $db->getConnection();
        }

        $classNameConnector = $this->getClassNameConnector(Helper::config('database')['default']);
        // BDD par defaut avec connector de config et avec ID de config
        if (self::$connection === null) {
            $db = new $classNameConnector(Helper::config('database')['default']);
            self::$connection = $db->getConnection();
        }

        return self::$connection;
    }

    private function getClassNameConnector(string $connector): mixed
    {
        $this->driver = Helper::config('database')['connections'][$connector]['driver'];

        switch ($this->driver) {
            case 'mysql':
                return 'DamianPhp\Database\Connectors\MySQL';
            case 'pgsql':
                return 'DamianPhp\Database\Connectors\PostgreSQL';
            default:
                Helper::getExceptionOrLog('Database Connection Name must be "mysql" or "pgsql".');
                return '';
        }
    }

    public function getConnector(): string
    {
        return $this->connector ?? Helper::config('database')['default'];
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * @param string|array $columnsParam - Colonnes à selectionner.
     */
    public function select(string|array $columnsParam): self
    {
        $columns = func_num_args() > 1 ? func_get_args() : $columnsParam;

        if (is_string($columns)) {
            $this->columns = $columns;
        } elseif (is_array($columns)) {
            $columnsToSelect = '';
            foreach ($columns as $column) {
                $columnsToSelect .= $column.', ';
            }

            $this->columns = rtrim($columnsToSelect, ', ');
        }

        return $this;
    }

    /**
     * Initialiser une request SQL SELECT.
     */
    public function setStartSelect(): void
    {
        $this->setStart("SELECT ".$this->columns." FROM ".$this->model->getDbTable());

        $this->sql['query_type'] = 'SELECT';
    }

    /**
     * Initialiser une request SQL SELECT aggregation.
     */
    public function setStartAggregation(string $aggregation): void
    {
        $this->setStart("SELECT ".$aggregation." AS nb FROM ".$this->model->getDbTable());

        $this->sql['query_type'] = 'SELECT';
    }

    /**
     * Initialiser une request SQL INSERT INTO.
     *
     * @param array $data - Colonnes où faire le INSERT, et valeurs à insérer.
     */
    public function setStartInsert(array $data): void
    {
        $columnsAndMarkers = $this->getColumnsAndGetMarkersForInsert($data);

        $this->setStart(
            "INSERT INTO ".$this->model->getDbTable()." (".$columnsAndMarkers['columns'].")
             VALUES (".$columnsAndMarkers['markers'].")"
        );

        $this->sql['query_type'] = 'INSERT';
    }

    /**
     * Initialiser une request SQL UPDATE.
     *
     * @param array $data - Colonnes où faire le UPDATE, et nouvelles valeurs à insérer.
     */
    public function setStartUpdate(array $data): void
    {
        $this->setStart("UPDATE ".$this->model->getDbTable()." SET ".$this->getColumnsForUpdate($data));

        $this->sql['query_type'] = 'UPDATE';
    }

    /**
     * Initialiser une request SQL DELETE.
     */
    public function setStartDelete(): void
    {
        $this->setStart("DELETE FROM ".$this->model->getDbTable());

        $this->sql['query_type'] = 'DELETE';
    }

    public function setStart(string $sql): void
    {
        $this->sql['start'] = " ".$sql." ";
    }

    /**
     * @param array $on - ON du JOIN.
     */
    public function addOn(array $on): self
    {
        $this->ons[] = $on;

        return $this;
    }

    /**
     * @param string $joinType - Table de jonction pour les requetes JOIN.
     * @param string $join - Table de jonction pour les requetes JOIN.
     */
    public function addJoin(string $joinType, string $join): void
    {
        $this->sql['join'] .= " ".$joinType." JOIN ".$join." ON ".$this->addOnToSql()." ";
    }

    /**
     * Pour éventuellement ajouter condition(s) avec WHERE (avec AND si plusieures conditions).
     */
    public function addWhere(array|string $where): void
    {
        if (is_array($where)) { // si array, on peut utiliser qu'une seule fois cette méthode avec une instance
            $this->wheres = $where;

            $this->sql['where'] .= " ".$this->logicOperator." ".$this->addConditionsToSql()." ";
        } else {
            $args = func_get_args();

            if (count($args) !== 3) {
                Helper::getExceptionOrLog('Method "where" must have 3 parameters if is not a multidimensional array.');
            }

            $this->wheres[] = $args;

            $this->sql['where'] .= " ".$this->logicOperator." ".$this->addConditionsToSql()." ";

            $this->logicOperator = " AND ";
        }
    }

    /**
     * Pour éventuellement ajouter condition(s) avec WHERE (avec OR si plusieures conditions).
     */
    public function addOrWhere(array $where): void
    {
        $this->logicOperator = " OR ";

        $this->addWhere($where[0], $where[1], $where[2]);
    }

    /**
     * Pour ajouter condition(s) avec WHERE IN (et avec AND si plusieures conditions).
     */
    public function addWhereIn(string $column, array $values): void
    {
        foreach ($values as $value) { // pour bindValue(s)
            $this->wheresIn[][2] = $value;
        }

        $this->sql['where'] .= " ".$this->logicOperator." ".$column." IN ".$this->addConditionsToSqlWhithWhereIn()." ";

        $this->logicOperator = " AND ";
    }

    /**
     * Pour ajouter condition(s) avec WHERE IN (et avec OR si plusieures conditions).
     */
    public function addOrWhereIn(string $column, array $values): void
    {
        $this->logicOperator = " OR ";

        $this->addWhereIn($column, $values);
    }

    /**
     * Pour éventuellement ajouter un orderBy avec un order.
     *
     * @param string $orderBy - Afficher par.
     * @param string $order - Ordre d'affichage.
     */
    public function addOrderBy(string $orderBy, string $order): void
    {
        if ($this->sql['orderby'] === null) {
            $this->sql['orderby'] = " ORDER BY ".$orderBy." ".$order." ";
        } else {
            $this->sql['orderby'] .= " , ".$orderBy." ".$order." ";
        }
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;

        $this->sql['limit'] = $limit !== null ? " LIMIT ? " : " ";
    }

    public function setOffset(?int $offset): void
    {
        $this->offset = $offset ?? 0;

        $this->sql['offset'] = $offset !== null ? " OFFSET ? " : " ";
    }

    /**
     * Démarre une transaction.
     */
    public function beginTransaction(): mixed
    {
        return $this->getConnection()->beginTransaction();
    }

    /**
     * Valide une transaction.
     */
    public function validTransaction(): mixed
    {
        return $this->getConnection()->commit();
    }

    /**
     * Annule une transaction.
     */
    public function cancelTransaction(): mixed
    {
        return $this->getConnection()->rollBack();
    }

    /**
     * Pour les requetes SQL avec jointure(s) - Préciser la condition de la jointure.
     * Boucle pour incrémenter les bindValue du ON, et des éventuels AND après le ON.
     *
     * @return string - Condition(s) de la jointure.
     */
    private function addOnToSql(): string
    {
        $paramsOn = '';

        foreach ($this->ons as $on) {
            $comporaisonOperator = $on[1];
            if (! in_array($comporaisonOperator, self::COMPARAISON_OPERATORS)) {
                Helper::getExceptionOrLog('Comparaison operator "'.$comporaisonOperator.'" not allowed.');
            }

            $paramsOn .= $on[0]." ".$comporaisonOperator." ".$on[2]." ";
        }

        $this->ons = [];

        return $paramsOn;
    }

    /**
     * Boucle pour si il y a condition(s) en WHERE - Incrémenter les champs à modifier + marquers de positionnement.
     * WHERE- $value[0] : marqueur / $value[1] : opérateur arithmétique / $value[2] : valeur / $value[3] opérateur logique.
     *
     * @return string - Soit '', ou soit colonne(s) où faire le(s) condition(s) + marquer(s) de positionnement.
     */
    private function addConditionsToSql(): string
    {
        $paramsWhere = '';

        if (count($this->wheres) > 0) {
            foreach ($this->wheres as $value) {
                $comporaisonOperator = $value[1];
                if (! in_array($comporaisonOperator, self::COMPARAISON_OPERATORS)) {
                    Helper::getExceptionOrLog('Comparaison operator "'.$comporaisonOperator.'" not allowed.');
                }

                $logicOperator = isset($value[3]) && $value[3] !== null ? $value[3] : '';
                if ($logicOperator !== '' && !in_array($logicOperator, self::LOGIC_OPERATORS)) {
                    Helper::getExceptionOrLog('Logic operator "'.$logicOperator.'" not allowed.');
                }

                $paramsWhere .= $value[0]." ".$comporaisonOperator." ? ".$logicOperator." ";

                $this->wheresBindValue[] = $value;
            }

            $this->wheres = [];
        }

        return $paramsWhere;
    }

    /**
     * Boucle pour si il y a condition(s) avec WHERE IN - Incrémenter les marquers de positionnement.
     *
     * @return string - Soit '', ou soit colonne(s) où faire le(s) marquer(s) de positionnement.
     */
    private function addConditionsToSqlWhithWhereIn(): string
    {
        if (count($this->wheresIn) === 0) {
            Helper::getExceptionOrLog('Argument 2 passed to "whereIn()" can not be an empty array.');
        }

        $paramsWhereIn = " ( ";

        foreach ($this->wheresIn as $value) {
            $paramsWhereIn .= " ?, ";

            $this->wheresBindValue[] = $value;
        }

        $this->wheresIn = [];

        return rtrim($paramsWhereIn, ', ')." ) ";
    }

    private function getColumnsAndGetMarkersForInsert(array $data): array
    {
        $paramsColumns = '';
        $markers = '';

        foreach ($data as $key => $value) {
            $paramsColumns .= ''.$key.', ';
            $markers .= ':'.$key.', ';
        }

        $columns = rtrim($paramsColumns, ', ');
        $markersTrim = rtrim($markers, ', ');

        return ['columns' => $columns, 'markers' => $markersTrim];
    }

    private function getColumnsForUpdate(array $data): string
    {
        $paramsColumns = '';

        foreach ($data as $key => $value) {
            $paramsColumns .= $key." = ?, " ;
        }

        return rtrim($paramsColumns, ', ');
    }

    public function resetSql(): void
    {
        $this->initSql();
    }

    /**
     * Pour les requetes SQL "complexes".
     *
     * @return mixed - Requete.
     */
    public function query(string $sql, array $otpions = []): object
    {
        $isAssociativeArray = function ($array) { // return true si c'est un array associatif
            if (!is_array($array) || empty($array)) {
                return false;
            }

            $keys = array_keys($array);

            return array_keys($keys) !== $keys;
        };

        $this->prepare($sql);

        if ($otpions) {
            if ($isAssociativeArray($otpions)) {
                foreach ($otpions as $key => $value) {
                    $this->bind($key, $value, $this->bindDataType($value));
                }
            } else {
                $i = 1;
                foreach ($otpions as $value) {
                    $this->bind($i, $value, $this->bindDataType($value));
                    $i++;
                }
            }
        }

        $this->sqlQuery->execute();

        return $this->sqlQuery;
    }

    /**
     * Préparer la requete SQL.
     */
    public function prepare(string $sql = null): self
    {
        $this->sqlQuery = $this->getConnection()->prepare($this->getSql($sql));

        return $this;
    }

    private function getSql(string $sql = null): string
    {
        if ($sql) {
            $sqlQuery = $sql;
        } else {
            $sqlQuery = $this->sql['start'].$this->sql['join'].$this->sql['where'];
            $sqlQuery .= $this->sql['orderby'];

            // avec PostgreSQL, LIMIT n'est pas un mot clé valide dans une instruction UPDATE ou DELETE
            if ($this->driver === 'pgsql' && in_array($this->sql['query_type'], ['UPDATE', 'DELETE'])) {
                $sqlQuery .= '';
                $this->sql['limit'] = null;
                $this->limit = null;
            } else {
                $sqlQuery .= $this->sql['limit'].$this->sql['offset'];
            }
        }

        return $sqlQuery;
    }

    /**
     * Les bindValue.
     */
    private function bind(int|string $key, mixed $value, bool|int $bindDataType): void
    {
        $this->sqlQuery->bindValue($key, $value, $bindDataType);
    }

    /**
     * Boucle pour incrémenter les bindValue des WHERE.
     */
    public function bindWhere(): self
    {
        if (count($this->wheresBindValue) > 0) {
            foreach ($this->wheresBindValue as $value) {
                $bindDataType = $this->bindDataType($value[2]);
                $this->sqlQuery->bindValue($this->positioningMarker, $value[2], $bindDataType);

                $this->positioningMarker++;
            }
        }

        return $this;
    }

    /**
     * Les bindValue du Limit.
     */
    public function bindLimit(): self
    {
        if ($this->limit !== null) {
            $this->sqlQuery->bindValue($this->positioningMarker, $this->limit, PDO::PARAM_INT);
            if ($this->offset !== null) { // pour à partir de PHP 8.0.1
                $this->positioningMarker++;
                $this->sqlQuery->bindValue($this->positioningMarker, $this->offset, PDO::PARAM_INT);
            }
        }

        return $this;
    }

    public function bindValuesForInsert(array $data): self
    {
        foreach ($data as $key => $value) {
            $this->sqlQuery->bindValue(':'.$key, $value, $this->bindDataType($value));
        }

        return $this;
    }

    public function bindSetForUpdate(array $data): self
    {
        foreach ($data as $value) {
            $this->sqlQuery->bindValue($this->positioningMarker, $value, $this->bindDataType($value));
            $this->positioningMarker++;
        }

        return $this;
    }

    /**
     * Utile pour les bindValue des requetes SQL.
     *
     * @param null|bool|int|string $value
     * @return bool|int
     */
    private function bindDataType(null|bool|int|string $value): bool|int
    {
        switch (true) {
            case is_string($value):
                return PDO::PARAM_STR;
            case is_int($value):
                return PDO::PARAM_INT;
            case is_bool($value):
                return PDO::PARAM_BOOL;
            case is_null($value):
                return PDO::PARAM_NULL;
            default:
                return false;
        }
    }

    /**
     * Exécuter la requete préparée.
     */
    public function execute(): void
    {
        $this->sqlQuery->execute();

        $this->positioningMarker = 1;
    }

    public function fetch(): mixed
    {
        return $this->sqlQuery->fetch();
    }

    /**
     * Fermer la connexion.
     *
     * @param null|mixed $sqlQuery - requete SQL à fermer.
     */
    public function close(mixed $sqlQuery = null): void
    {
        if ($sqlQuery) {
            $sqlQuery->closeCursor();
        } else {
            $this->sqlQuery->closeCursor();
        }
    }

    /**
     * Pour si on veut récupérer le nombre de lignes affectées.
     */
    public function setRowCount(bool $runRowCount): ?int
    {
        if ($runRowCount === true) {
            return $this->sqlQuery->rowCount();
        }

        return null;
    }

    /**
     * @return int - Dernier ID inséré par auto-incrémentation.
     */
    public function getLastInsertId(): int
    {
        return $this->getConnection()->lastInsertId();
    }

    public function getSqlQuery(): mixed
    {
        return $this->sqlQuery;
    }
}
