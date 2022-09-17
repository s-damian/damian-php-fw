<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la classe Response.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 *
 * @method static void header(string $content, string $type = null)
 */
final class Response extends Facade
{
    /**
     * @var \DamianPhp\Http\Response\Response
     */
    protected static $instance;

    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Http\Response\Response::class;
    }
}
