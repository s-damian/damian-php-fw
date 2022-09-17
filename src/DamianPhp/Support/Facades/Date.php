<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour les Date.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 *
 * @method static string getDateTimeFormat(string $format = 'Y-m-d H:i:s')
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
