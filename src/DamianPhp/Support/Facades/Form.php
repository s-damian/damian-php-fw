<?php

namespace DamianPhp\Support\Facades;

/**
 * Facade pour les helpers de la classe Form.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Form extends Facade
{
    /**
     * @var \DamianPhp\Form\Form
     */
    protected static $instance;

    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \DamianPhp\Form\Form::class;
    }
}
