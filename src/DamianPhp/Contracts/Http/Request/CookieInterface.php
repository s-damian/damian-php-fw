<?php

declare(strict_types=1);

namespace DamianPhp\Contracts\Http\Request;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface CookieInterface
{
    /**
     * Créer un cookie.
     *
     * @param string $name - Nom du cookie.
     * @param string $value - Valeur du cookie.
     * @param string|null $minutes - Durée du cookie, temps après lequel le cookie expire. A spécifier en minuttes.
     * @param string $path - Le chemin sur le serveur sur lequel le cookie sera disponible.
     * @param string|null $domain - Le domaine pour lequel le cookie est disponible.
     * @param bool|null $secure - Indique si le cookie doit uniquement être transmis à travers une connexion sécurisée HTTPS depuis le client.
     * @param bool|null $httponly - Lorsque ce paramètre vaut TRUE, le cookie ne sera accessible que par le protocole HTTP (ne sera pas éditable en JS...).
     */
    public function put(string $name, string $value = '', int $expire = null, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = true);

    public function has(string $name): bool;

    public function get(string $name): mixed;

    /**
     * Supprimer un cookie.
     *
     * @param string $name - Nom du cookie.
     * @param string|null $path - Le chemin sur le serveur sur lequel le cookie sera disponible.
     * @param string|null $domain - Le domaine pour lequel le cookie est disponible.
     */
    public function destroy(string $name, string $path = '/', string $domain = ''): void;
}
