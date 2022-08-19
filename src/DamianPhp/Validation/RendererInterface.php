<?php

namespace DamianPhp\Validation;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface RendererInterface
{
    /**
     * @return string - Les erreurs à retourner.
     */
    public function getErrors(): string;

    /**
     * @return string - Le message de confirmation.
     */
    public function getSuccess(): string;
}
