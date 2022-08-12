<?php

namespace DamianPhp\Database\Connectors;

use DamianPhp\Support\Helper;

/**
 * Pour se connecter à une BDD du SGBDR PostgreSQL.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class PostgreSQL extends Connector
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
            $this->username = $id['username'];
            $this->password = $id['password'];
        } else {
            $host = Helper::config('database')['connections'][$this->connector]['host'];
            $database = Helper::config('database')['connections'][$this->connector]['database'];
            $this->username = Helper::config('database')['connections'][$this->connector]['username'];
            $this->password = Helper::config('database')['connections'][$this->connector]['password'];
        }

        $this->setDsn($host, $database);
    }

    private function setDsn(string $host, string $database): void
    {
        $this->dsn = 'pgsql:host='.$host.';';

        if (isset(Helper::config('database')['connections'][$this->connector]['port'])) {
            $this->dsn .= 'port='.Helper::config('database')['connections'][$this->connector]['port'].';';
        }
        
        $this->dsn .= 'dbname='.$database.';'; 
    }
}
