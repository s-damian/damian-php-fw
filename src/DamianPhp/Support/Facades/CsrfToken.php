<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la classe CsrfToken.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class CsrfToken extends Facade
{
    /**
     * @var \DamianPhp\Support\Security\CsrfToken
     */
    protected static $instance;

    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Support\Security\CsrfToken::class;
    }
}
