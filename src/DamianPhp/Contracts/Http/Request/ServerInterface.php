<?php

namespace DamianPhp\Contracts\Http\Request;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface ServerInterface
{
    public function __construct();

    public function get(string $key): array|string;

    public function getMethod(): string;
    
    /**
     * @return string - L'URI qui a été fourni pour accéder à cette page.
     */
    public function getRequestUri(): string;

    /**
     * @return string - URI (URL après nom de domaine du site, sans le slash de gauche).
     */
    public function getUri(): string;

    /**
     * @return string - Le nom du serveur hôte qui exécute le script suivant.
     */
    public function getServerName(): string;

    /**
     * @return string - Contenu de l'en-tête Host: de la requête courante, si elle existe.
     */
    public function getHttpHost(): string;

    /**
     * @return string - L'URL courante (sans les éventuels query params).
     */
    public function getUrlCurrent(): string;

    /**
     * @return string
     */
    public function getDocumentRoot(): string;

    /**
     * @return string - Adresse IP d'un visiteur.
     */
    public function getIp(): string;

    /**
     * @return string - Contenu de l'en-tête User_Agent: de la requête courante, si elle existe.
     */
    public function getHttpUserAgent(): string;
}
