<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour les String.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Str extends Facade
{
    /**
     * @var \DamianPhp\Support\String\Str
     */
    protected static $instance;

    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Support\String\Str::class;
    }
}
