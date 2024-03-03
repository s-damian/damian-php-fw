<?php

declare(strict_types=1);

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la classe Cookie.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 *
 * @method static bool has(string $name)
 */
final class Cookie extends Facade
{
    /**
     * @var \DamianPhp\Http\Request\Cookie
     */
    protected static $instance;

    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Http\Request\Cookie::class;
    }
}
