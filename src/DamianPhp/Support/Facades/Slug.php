<?php

declare(strict_types=1);

namespace DamianPhp\Support\Facades;

/**
 * Facade pour les slugs.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Slug extends Facade
{
    /**
     * @var \DamianPhp\Support\String\Slug
     */
    protected static $instance;

    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Support\String\Slug::class;
    }
}
