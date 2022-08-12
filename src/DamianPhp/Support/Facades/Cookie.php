<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la classe Cookie.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Cookie extends Facade
{
    /**
     * @var \DamianPhp\Http\Request\Cookie
     */
    protected static $instance;

    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Http\Request\Cookie::class;
    }
}
