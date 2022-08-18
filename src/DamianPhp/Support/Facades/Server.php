<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la classe Server.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Server extends Facade
{
    /**
     * @var \DamianPhp\Http\Request\Server
     */
    protected static $instance;

    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Http\Request\Server::class;
    }
}
