<?php

namespace DamianPhp\Database\Connectors;

use PDO;
use PDOException;
use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Log;
use DamianPhp\Support\Facades\Response;
use DamianPhp\Contracts\Database\Connectors\ConnectorInterface;

/**
 * Classe patrent des différents Connectors.
 * Pour récupérer une connexion à une BDD avec PDO.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
abstract class Connector implements ConnectorInterface
{
    private PDO $connection;

    protected string $connector;

    protected string $dsn;

    protected string $username;

    protected string $password;

    protected array $options = [];

    /**
     * DSN + Identification + Options - Iront dans paramètres de l'instance de PDO.
     */
    abstract public function __construct(string $connector, array $id = []);

    final public function getConnection(): PDO
    {
        try {
            $this->connection = new PDO($this->dsn, $this->username, $this->password, $this->getOptions());

            $this->ifDebug();
        } catch (PDOException $e) {
            $this->ifCatch($e);
        }

        return $this->connection;
    }

    /**
     * Ajouter des options à l'instance de PDO.
     */
    private function getOptions(): array
    {
        return $this->options + [
            // Laisse les noms de colonnes à une case inchangée.
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            // Récupère la prochaine ligne et la retourne en tant qu'objet.
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            // Désactiver l'émulation de requêtes préparées.
            PDO::ATTR_EMULATE_PREPARES => false,
            // Pas de conversions pour les valeurs NULL et chaînes vides.
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
            // Ne pas convertir une valeur numérique en chaîne lors de la lecture.
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ];
    }

    private function ifDebug(): void
    {
        if (Helper::config('app')['debug']) {
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, Helper::config('database')['debug']);
        }
    }

    private function ifCatch(PDOException $e): never
    {
        Log::errorDamianPhp('PDOException in class in '.get_class().' on line '.__LINE__.'. Error message: '.$e->getMessage());

        if (Helper::config('app')['debug']) {
            exit('Error: '.$e->getMessage());
        } else {
            Response::header('HTTP/1.1 503 Service Temporarily Unavailable');
            Response::header('Status: 503 Service Temporarily Unavailable');
            Response::header('Retry-After: 300');
            
            exit(Helper::lang('database')['connection_error']);
        }
    }
}
