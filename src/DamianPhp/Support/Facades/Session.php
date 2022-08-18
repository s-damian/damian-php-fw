<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la classe Session.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Session extends Facade
{
    /**
     * @var \DamianPhp\Session\Session
     */
    protected static $instance;

    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Session\Session::class;
    }
}
