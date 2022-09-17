<?php

namespace DamianPhp\Session;

use DamianPhp\Support\Facades\Server;
use DamianPhp\Contracts\Session\SessionInterface;

/**
 * Classe client.
 * Gestion des sessions.
 * Peut fonctionner avec une Facade.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Session implements SessionInterface
{
    private SessionStorage $sessionStorage;

    public function __construct()
    {
        $this->sessionStorage = new SessionStorage();

        $this->sessionStorage->put('_url', Server::getRequestUri()); // savegarder URL actuelle dans une session.
    }

    /**
     * Démarrer session.
     */
    public function start(): bool
    {
        return $this->sessionStorage->start();
    }

    /**
     * Créer une session.
     *
     * @param string $name - Nom de la session.
     * @param mixed $value - Caleur de la session.
     */
    public function put(string $name, mixed $value): void
    {
        $this->sessionStorage->put($name, $value);
    }

    /**
     * @return array - Toutes les sessions.
     */
    public function all(): array
    {
        return $this->sessionStorage->all();
    }

    /**
     * @return array - Les keys des sessions.
     */
    public function keys(): array
    {
        return $this->sessionStorage->keys();
    }

    /**
     * @return int - Le nombre de sessions.
     */
    public function count(): int
    {
        return $this->sessionStorage->count();
    }

    /**
     * Verifier si une session existe.
     *
     * @param string $name - Nom de la session.
     */
    public function has(string $name): bool
    {
        return $this->sessionStorage->has($name);
    }

    /**
     * @param string $name - Nom de la session.
     */
    public function get(string $name, $default = ''): mixed
    {
        return $this->sessionStorage->get($name);
    }

    /**
     * Supprimer une session.
     *
     * @param string $name - Nom de la session.
     */
    public function destroy(string $name): void
    {
        $this->sessionStorage->destroy($name);
    }

    /**
     * Supprimer Toutes les session (lorqu'on déconnecte un utilisateur par exemple).
     */
    public function clear(): void
    {
        $this->sessionStorage->clear();
    }

    /**
     * @return string - Identifiant de la session courante.
     */
    public function getId(): string
    {
        return $this->sessionStorage->getId();
    }

    /**
     * Remplace l'identifiant de la session courante par un nouveau.
     */
    public function regenerateId(): void
    {
        $this->sessionStorage->regenerateId();
    }
}
