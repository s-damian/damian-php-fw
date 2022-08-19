<?php

namespace DamianPhp\Database\Connectors;

use PDO;
use DamianPhp\Support\Helper;

/**
 * Pour se connecter à une BDD du SGBDR MySQL.
 *
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class MySQL extends Connector
{
    /**
     * DSN + Identification + Options - Iront dans les paramètres de l'instance de PDO.
     */
    public function __construct(string $connector, array $id = [])
    {
        $this->connector = $connector;

        if ($id !== []) {
            $host = $id['host'];
            $database = $id['database'];
            $unix_socket = $id['unix_socket'];
            $this->username = $id['username'];
            $this->password = $id['password'];
        } else {
            $host = Helper::config('database')['connections'][$this->connector]['host'];
            $database = Helper::config('database')['connections'][$this->connector]['database'];
            $unix_socket = Helper::config('database')['connections'][$this->connector]['unix_socket'];
            $this->username = Helper::config('database')['connections'][$this->connector]['username'];
            $this->password = Helper::config('database')['connections'][$this->connector]['password'];
        }

        $this->setDsn($host, $database, $unix_socket)->setOptions();
    }

    private function setDsn(string $host, string $database, ?string $unix_socket): self
    {
        if ($unix_socket !== null && file_exists($unix_socket)) {
            $this->dsn = 'mysql:unix_socket='.$unix_socket.';';
        } else {
            $this->dsn = 'mysql:host='.$host.';';

            if (isset(Helper::config('database')['connections'][$this->connector]['port'])) {
                $this->dsn .= 'port='.Helper::config('database')['connections'][$this->connector]['port'].';';
            }
        }

        $this->dsn .= 'dbname='.$database.';';
        $this->dsn .= 'charset='.Helper::config('database')['connections'][$this->connector]['charset'];

        return $this;
    }

    /**
     * Ajouter des otpions à PDO spécifiques au connector MySQL.
     */
    private function setOptions(): void
    {
        $this->options = [
            // PHP >= 5.6.5 - Empecher exécution de plusieurs requêtes en même temps, doit être dans driver_options
            PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
        ];
    }
}
