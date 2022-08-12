<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la class Request.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Request extends Facade
{
    /**
     * @var \DamianPhp\Http\Request\Request
     */
    protected static $instance;

    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Http\Request\Request::class;
    }
}
