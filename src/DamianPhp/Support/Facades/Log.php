<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la classe Log.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 * 
 * @method static void errorDamianPhp(string $message)
 */
final class Log extends Facade
{
    /**
     * @var \DamianPhp\Log\Log
     */
    protected static $instance;

    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Log\Log::class;
    }
}
