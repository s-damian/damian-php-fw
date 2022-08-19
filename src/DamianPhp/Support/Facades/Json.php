<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la classe Json.
 *
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Json extends Facade
{
    /**
     * @var \DamianPhp\Http\Response\Json
     */
    protected static $instance;

    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Http\Response\Json::class;
    }
}
