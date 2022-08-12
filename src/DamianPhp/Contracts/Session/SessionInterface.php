<?php

namespace DamianPhp\Contracts\Session;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
Interface SessionInterface
{
    /**
     *  Démarrer session.
     */
    public function start(): bool;
    
    /**
     * Créer une session.
     *
     * @param string $name - Nom de la session.
     * @param mixed $value - Caleur de la session.
     */
    public function put(string $name, mixed $value): void;

    /**
     * @return array - Toutes les sessions.
     */
    public function all(): array;

    /**
     * @return array - Les keys des sessions.
     */
    public function keys(): array;

    /**
     * @return int - Le nombre de sessions.
     */
    public function count(): int;

    /**
     * Verifier si une session existe.
     * 
     * @param string $name - Nom de la session.
     */
    public function has(string $name): bool;

    /**
     * @param string $name - Nom de la session.
     */
    public function get(string $name, $default = ''): mixed;

    /**
     * Supprimer une session.
     *
     * @param string $name - Nom de la session.
     */
    public function destroy(string $name): void;

    /**
     * Supprimer Toutes les session (lorqu'on déconnecte un utilisateur par exemple).
     */
    public function clear(): void;
    
    /**
     * @return string - Identifiant de la session courante.
     */
    public function getId(): string;
    
    /**
     * Remplace l'identifiant de la session courante par un nouveau.
     */
    public function regenerateId(): void;
}
