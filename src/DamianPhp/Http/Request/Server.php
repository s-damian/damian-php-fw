<?php

namespace DamianPhp\Http\Request;

use DamianPhp\Contracts\Http\Request\ServerInterface;

/**
 * Server.
 * Peut fonctionner avec une Facade.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Server implements ServerInterface
{
    private Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    public function get(string $key): array|string
    {
        return $this->request->getServer()->get($key);
    }

    public function getMethod(): string
    {
        return $this->request->getServer()->get('REQUEST_METHOD');
    }

    /**
     * @return string - L'URI qui a été fourni pour accéder à cette page.
     */
    public function getRequestUri(): string
    {
        return $this->request->getServer()->get('REQUEST_URI');
    }

    /**
     * @return string - URI (URL après nom de domaine du site, sans le slash de gauche).
     */
    public function getUri(): string
    {
        return ltrim($this->getRequestUri(), '/');
    }

    /**
     * @return string - Le nom du serveur hôte qui exécute le script suivant.
     */
    public function getServerName(): string
    {
        return $this->request->getServer()->get('SERVER_NAME');
    }

    public function getServerSoftware(): string
    {
        return $this->request->getServer()->get('SERVER_SOFTWARE');
    }

    /**
     * @return string - Contenu de l'en-tête Host: de la requête courante, si elle existe.
     */
    public function getHttpHost(): string
    {
        return $this->request->getServer()->get('HTTP_HOST');
    }

    /**
     * @return string - Nom de domaine (sans "www.").
     */
    public function getDomainName(): string
    {
        return ltrim($this->getHttpHost(), 'www.');
    }

    public function getDocumentRoot(): string
    {
        return $this->request->getServer()->get('DOCUMENT_ROOT');
    }

    public function getRequestScheme(): string
    {
        return $this->request->getServer()->get('REQUEST_SCHEME');
    }

    /**
     * @return string - Adresse IP d'un visiteur.
     */
    public function getIp(): string
    {
        return (filter_var($this->request->getServer()->get('REMOTE_ADDR'), FILTER_VALIDATE_IP))
            ? $this->request->getServer()->get('REMOTE_ADDR')
            : 'Unknown IP';
    }

    /**
     * @return string - Contenu de l'en-tête User_Agent: de la requête courante, si elle existe.
     */
    public function getHttpUserAgent(): string
    {
        return $this->request->getServer()->get('HTTP_USER_AGENT');
    }
}
