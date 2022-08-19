<?php

namespace DamianPhp\Contracts\Exception;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface ExceptionHandlerInterface
{
    /**
     * Renvoyer une exception avec un message d'erreur si on est en dev.
     */
    public function getException(string $message): void;

    /**
     * Si on est en dev : Renvoyer une exception avec un message d'erreur
     * Si on est en prod : Error 404
     *
     * @param string $message
     * @throws ExceptionHandler
     */
    public function getExceptionOrGetError404(string $message): void;

    /**
     * Retourne l'action d'erreur 404.
     */
    public function getError404(): mixed;
}
