<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour les routes du Router.
 * Doit impérativement extends de Facade (qui retourne qu'une seule instance).
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Router extends Facade
{
    /**
     * @var \DamianPhp\Routing\Router
     */
    protected static $instance;

    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Routing\Router::class;
    }
}
