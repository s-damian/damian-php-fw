<?php

namespace DamianPhp\Contracts\Auth;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface AuthInterface
{
    public function __construct(string $modelName);

    /**
     * OPTIONAL
     * Pour éventuellement laisser la possibilitée aux users d'avoir une connexion perraine.
     */
    public function remember(string $cookieNameRemember): self;

    /**
     * Connexion de l'user.
     *
     * @param string $sessionName - Nom de la session Auth.
     * @param array $valuesSession - Valeurs à envoyer à la session.
     */
    public function connect(string $sessionName, array $valuesSession): self;
}
