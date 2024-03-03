<?php

declare(strict_types=1);

namespace DamianPhp\Contracts\Auth;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface IsConnectedInterface
{
    /**
     * @param string $modelName - Model où faire les requetes SQL.
     */
    public function __construct(string $modelName);

    /**
     * @param string $sessionName - Nom de la session.
     * @param array $sessionValue - Valeur de la session sous forme de array numéroté.
     * @param array $valuesIntForRegenerateSession.
     */
    public function session(string $sessionName, array $sessionValue, array $valuesIntForRegenerateSession = []): self;

    /**
     * OPTIONAL (utile uniquement si on laisse un "Se souvenir de moi" au login).
     *
     * @param string $cookieName - Nom du cookie.
     */
    public function cookie(string $cookieName): self;

    /**
     * @param string $urlToredirectIfFalse - URL de redirection si IsLogged return false.
     */
    public function urlToredirectIfFalse(string $urlToredirectIfFalse): self;

    /**
     * Tester si l'user est identifié ou non.
     */
    public function isLogged(): bool;

    /**
     * Exit.
     */
    public function exit();
}
