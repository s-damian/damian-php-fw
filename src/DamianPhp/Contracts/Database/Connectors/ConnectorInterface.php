<?php

namespace DamianPhp\Contracts\Database\Connectors;

use PDO;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface ConnectorInterface
{
    public function getConnection(): PDO;
}
