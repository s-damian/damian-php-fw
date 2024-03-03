<?php

declare(strict_types=1);

namespace DamianPhp\Contracts\Config;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface SingletonConfigInterface
{
    /**
     * Singleton.
     */
    public static function getInstance(): object;
}
