<?php

declare(strict_types=1);

namespace DamianPhp\Support\Facades;

/**
 * Facade pour la classe Media.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Media extends Facade
{
    /**
     * @var \DamianPhp\Support\String\Media
     */
    protected static $instance;

    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Support\String\Media::class;
    }
}
