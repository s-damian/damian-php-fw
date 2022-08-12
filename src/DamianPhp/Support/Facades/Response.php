<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la classe Response.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Response extends Facade
{
    /**
     * @var \DamianPhp\Http\Response\Response
     */
    protected static $instance;

    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Http\Response\Response::class;
    }
}
