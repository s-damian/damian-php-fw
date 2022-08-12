<?php

namespace DamianPhp\Contracts\Database\Connectors;

use PDO;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
Interface ConnectorInterface
{   
    public function getConnection(): PDO;
}
