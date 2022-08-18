<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la classe Cache.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Cache extends Facade
{
    /**
     * @var \DamianPhp\Cache\Cache
     */
    protected static $instance;

    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Cache\Cache::class;
    }
}
