<?php

declare(strict_types=1);

namespace DamianPhp\Http\Request;

use DamianPhp\Support\Helper;
use DamianPhp\Contracts\Http\Request\CookieInterface;

/**
 * Gestion des cookies.
 * Peut fonctionner avec une Facade.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Cookie implements CookieInterface
{
    private Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

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
    public function put(string $name, string $value = '', ?int $minutes = null, ?string $path = null, ?string $domain = null, ?bool $secure = null, ?bool $httponly = null)
    {
        if ($minutes === 0) {
            $expireValue = 0;
        } elseif ($minutes === null) {
            $expireValue = time() + (Helper::config('cookie')['expire'] * 60);
        } else {
            $expireValue = time() + ($minutes * 60);
        }

        $pathValue = $path ?? Helper::config('cookie')['path'];

        $domainValue = $domain ?? Helper::config('cookie')['domain'];

        $secureValue = $secure ?? Helper::config('cookie')['secure'];

        $httponlyValue = $httponly ?? Helper::config('cookie')['httponly'];

        setcookie($name, $value, (int) $expireValue, $pathValue, $domainValue, $secureValue, $httponlyValue);
    }

    public function has(string $name): bool
    {
        return $this->request->getCookies()->has($name);
    }

    public function get(string $name): mixed
    {
        return $this->request->getCookies()->get($name);
    }

    /**
     * Supprimer un cookie.
     *
     * @param string $name - Nom du cookie.
     * @param string|null $path - Le chemin sur le serveur sur lequel le cookie sera disponible.
     * @param string|null $domain - Le domaine pour lequel le cookie est disponible.
     */
    public function destroy(string $name, ?string $path = null, ?string $domain = null): void
    {
        $pathValue = $path ?? Helper::config('cookie')['path'];

        $domainValue = $domain ?? Helper::config('cookie')['domain'];

        $this->put($name, '', -2628000, $pathValue, $domainValue);
    }
}
