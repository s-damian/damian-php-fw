<?php

declare(strict_types=1);

namespace DamianPhp\Support\Facades;

/**
 * Classe parent de Toutes les Façades (où on veut qu'une seule instance dans toute l'application).
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
abstract class Facade
{
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

    abstract protected static function getFacadeAccessor(): string;

    private static function getFacadeInstace(): object
    {
        $class = static::getFacadeAccessor();

        return new $class();
    }
}
