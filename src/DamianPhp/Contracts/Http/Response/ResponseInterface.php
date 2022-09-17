<?php

namespace DamianPhp\Contracts\Http\Response;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface ResponseInterface
{
    /**
     * @param int - Code de la réponse HTTP.
     */
    public function getHttpResponseCode(): int;

    /**
     * Spécifier l'en-tête HTTP de l'affichage d'une vue.
     */
    public function header(string $content, string $type = null): void;

    /**
     * Rediriger.
     */
    public function redirect(string $url, int $httpResponseCodeParam = null);

    /**
     * Retourner le contenu d'un fichier .php en string.
     *
     * @param string $path - Path du fichier.
     * @param array|null $data - Pour renvoyer les éventuelles données à la vue.
     * @return string - Contenu d'un fichier.
     */
    public function share(string $path, array $data = []);

    /**
     * Pour les messages de confirmation.
     */
    public function alertSuccess(string $message);

    /**
     * Pour les messages d'erreur.
     */
    public function alertError(string $message);

    /**
     * Pour les messages de confirmations ou d'erreurs dans le site.
     *
     * @param $css (string) - class CSS.
     * @param string $message - Message(s) d'info ou d'erreur.
     */
    public function setAlert(string $css, string $message);
}
