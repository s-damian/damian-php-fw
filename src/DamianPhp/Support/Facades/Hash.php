<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la classe Hash.
 *
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Hash extends Facade
{
    /**
     * @var \DamianPhp\Hashing\Hash
     */
    protected static $instance;

    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Hashing\Hash::class;
    }
}
