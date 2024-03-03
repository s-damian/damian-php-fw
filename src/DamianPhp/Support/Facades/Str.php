<?php

declare(strict_types=1);

namespace DamianPhp\Support\Facades;

/**
 * Facade pour les String.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 *
 * @method static string convertCamelCaseToSnakeCase(string $value)
 * @method static bool contains(string $haystack,  string $needle)
 * @method static string snakePlural(string $value)
 * @method static string random(int $nbChars = 10, array $options = [])
 */
final class Str extends Facade
{
    /**
     * @var \DamianPhp\Support\String\Str
     */
    protected static $instance;

    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Support\String\Str::class;
    }
}
