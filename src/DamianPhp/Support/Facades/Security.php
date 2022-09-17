<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la classe Security.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Security extends Facade
{
    /**
     * @var \DamianPhp\Support\Security\Security
     */
    protected static $instance;

    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Support\Security\Security::class;
    }
}
