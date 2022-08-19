<?php

namespace DamianPhp\Session;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Server;

/**
 * Storage des sessions
 *
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class SessionStorage
{
    /**
     * @var bool
     */
    private bool $started = false;

    /**
     * Démarrer session.
     */
    public function start(): bool
    {
        if (Server::getRequestScheme() === 'https') {
            ini_set('session.cookie_secure', '1');
            ini_set('session.cookie_httponly', '1');
        }

        if ($this->started) {
            return true;
        }

        if (PHP_SESSION_ACTIVE === session_status()) {
            Helper::getExceptionOrLog('Failed to start the session: already started by PHP.');
        }

        if (! session_start()) {
            Helper::getExceptionOrLog('Failed to start the session.');
        }

        $this->started = true;

        return true;
    }

    /**
     * Créer une session
     *
     * @param string $name - Nom de la session.
     * @param mixed $value - Caleur de la session.
     */
    public function put(string $name, mixed $value): void
    {
        $_SESSION[$name] = $value;
    }

    /**
     * @return array - Toutes les sessions.
     */
    public function all(): array
    {
        return $_SESSION;
    }

    /**
     * @return array - Les keys des sessions.
     */
    public function keys(): array
    {
        return array_keys($_SESSION);
    }

    /**
     * @return int - Le nombre de sessions.
     */
    public function count(): int
    {
        return count($_SESSION);
    }

    /**
     * Verifier si une session existe.
     *
     * @param string $name - Nom de la session.
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $_SESSION);
    }

    /**
     * @param string $name - Nom de la session.
     */
    public function get(string $name, $default = ''): mixed
    {
        return $this->has($name) ? $_SESSION[$name] : $default;
    }

    /**
     * Supprimer une session.
     *
     * @param string $name - Nom de la session.
     */
    public function destroy(string $name): void
    {
        if ($this->has($name)) {
            unset($_SESSION[$name]);
        }
    }

    /**
     * Supprimer Toutes les session (lorqu'on déconnecte un utilisateur par exemple).
     */
    public function clear(): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    /**
     * @return string - Identifiant de la session courante.
     */
    public function getId(): string
    {
        return session_id();
    }

    /**
     * Remplace l'identifiant de la session courante par un nouveau.
     */
    public function regenerateId(): void
    {
        session_regenerate_id();
    }
}
