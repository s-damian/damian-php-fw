<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour les Date.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Date extends Facade
{
    /**
     * @var \DamianPhp\Date\Date
     */
    protected static $instance;

    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Date\Date::class;
    }
}
