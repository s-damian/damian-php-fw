<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la classe Input.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Input extends Facade
{
    /**
     * @var \DamianPhp\Http\Request\Input
     */
    protected static $instance;

    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Http\Request\Input::class;
    }
}
