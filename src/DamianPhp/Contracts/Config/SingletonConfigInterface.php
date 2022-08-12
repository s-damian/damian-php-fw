<?php

namespace DamianPhp\Contracts\Config;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
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
