<?php

namespace DamianPhp\Support\Facades;

/**
 * Classe parent de Toutes les Façades (où on veut qu'une seule instance dans toute l'application).
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
abstract class Facade
{
    abstract protected static function getFacadeAccessor(): string;
    
    /**
     * @param string $method - Nom de la méthode à appeler
     * @param array $arguments - Paramètres dans méthodes
     * @return mixed
     */
    final public static function __callStatic(string $method, array $arguments)
    {
        if (static::$instance === null) {            
            static::$instance = self::getFacadeInstace();
        }

        return static::$instance->$method(...$arguments);
    }

    private static function getFacadeInstace(): object
    {
        $class = static::getFacadeAccessor();
        
        return new $class();
    }
}
