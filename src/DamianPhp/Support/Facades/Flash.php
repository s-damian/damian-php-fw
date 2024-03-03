<?php

declare(strict_types=1);

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la classe Flash.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Flash extends Facade
{
    /**
     * @var \DamianPhp\Session\Flash
     */
    protected static $instance;

    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Session\Flash::class;
    }
}
