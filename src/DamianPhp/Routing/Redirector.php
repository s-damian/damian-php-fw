<?php

namespace DamianPhp\Routing;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Str;
use DamianPhp\Support\Facades\Server;
use DamianPhp\Support\Facades\Response;
use DamianPhp\Contracts\Routing\RouterInterface;

/**
 * Redirections.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Redirector
{
    /**
     * L'instance router.
     */
    private RouterInterface $router;

    /**
     *  Redirectorconstructor.
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * - si "&" dans l'URL mais pas de "?" : Erreur 404.
     *
     * - si plusieurs slachs consécutifs ont été entrés dans l'URL :
     *      Si 'internationalization' est activée, et que 'address_structure' est = 'subdirectories', et qu'on est sur la homepage du site ->
     *           en laisser que un à la place à la fin.
     *      Si on n'est pas sur homepage (et peut importe si 'internationalization' est activée) ->
     *           en laisser que un à la place, sauf si c'est à la fin on les supprime tous.
     *
     * - Si l'internationalization' est activée, et que 'address_structure' est = 'subdomain,
     *   et que si on a pas précisé la lang en sous-domaine, "ajouter" la langue par default en sous-domaine
     */
    public function verifyUrlBeforeAttemptMatching(): void
    {
        if (Str::contains(Server::getUri(), '&') && !Str::contains(Server::getUri(), '?')) {
            Helper::getExceptionOrGetError404('URI has "&" but not "?".');
        }

        // s'il y a 1 "?" qui traine en fin d'URL, l'enlever
        if (mb_substr(Server::getUri(), -1) === '?') {
            $urlToRedirect = Helper::getBaseUrl().'/'.$this->router->getUri();
            Response::redirect($urlToRedirect, 301);
        }

        $this->ifMultipleSlashesInUri();

        $this->ifIsMultilingualAndAddressStructureIsSubdomainVarifyIsHasLang();
    }

    /**
     * - si plusieurs slachs consécutifs ont été entrés dans l'URL :
     *      Si 'internationalization' est activée, et que 'address_structure' est = 'subdirectories', et qu'on est sur la homepage du site ->
     *           en laisser que un à la place à la fin.
     *      Si on n'est pas sur homepage (et peut importe si 'internationalization' est activée) ->
     *           en laisser que un à la place, sauf si c'est à la fin on les supprime tous.
     */
    private function ifMultipleSlashesInUri(): void
    {
        if (Str::contains(Server::getRequestUri(), '//')) {
            if (
                Helper::isMultilingual() &&
                Helper::config('lang')['address_structure'] === 'subdirectories' &&
                rtrim($this->router->getUri(), '/') === $this->router->getLang()
                )
            {
                $urlToRedirect = Helper::getBaseUrl().'/'.rtrim($this->router->getUri(), '/').'/';
            } else {
                $uri = str_replace('//', '/', $this->router->getUri());
                $urlToRedirect = Helper::getBaseUrl().'/'.rtrim($uri, '/');
            }

            Response::redirect($urlToRedirect, 301);
        }
    }

    /**
     * - Si l'internationalization' est activée, et que 'address_structure' est = 'subdomain,
     *   et que si on a pas précisé la lang en sous-domaine, "ajouter" la langue par default en sous-domaine.
     */
    private function ifIsMultilingualAndAddressStructureIsSubdomainVarifyIsHasLang(): void
    {
        if (Helper::isMultilingual() && Helper::config('lang')['address_structure'] === 'subdomain') {
            if (Str::contains(Server::getHttpHost(), $this->router->getLang().'.')) {
                $getBaseUrlWithoutWww = str_replace('www.', '', Helper::getActiveUrl());
                $serverWithoutWww = str_replace('www.', '', Server::getHttpHost());
                if (Str::contains(Server::getHttpHost(), 'www.')) {
                    $urlToRedirect = str_replace($serverWithoutWww, $this->router->getLang().'.'.$serverWithoutWww, Helper::getActiveUrl());
                } else {
                    $urlToRedirect = str_replace($serverWithoutWww, $this->router->getLang().'.'.$serverWithoutWww, $getBaseUrlWithoutWww);
                }

                Response::redirect($urlToRedirect, 302);
            }
        }
    }

    /**
     * - Si 'internationalization' est activée, et que 'address_structure' est = 'subdirectories', et qu'on est sur la homepage du site,
     *   et que si on a pas précisé la lang, ou qu'on "oublis" le slash après lang -> "ajouter" en sous-repertoire la langue par défault et après ajouter un slash.
     *
     * - Si non, si dans URL il y a un slash à la fin -> le "supprimer".
     */
    public function lastVerifyUrlBeforeGetErrorMatching(): void
    {
        if (
            Helper::isMultilingual() &&
            Helper::config('lang')['address_structure'] === 'subdirectories' &&
            ($this->router->getUri() === '' || $this->router->getUri() === $this->router->getLang())
            )
        {
            Response::redirect(Helper::getBaseUrl().'/'.$this->router->getLang().'/', 302);
        } elseif (mb_substr($this->router->getUri(), -1, 1) === '/') {
            Response::redirect(Helper::getBaseUrl().'/'.rtrim($this->router->getUri(), '/'), 301);
        }
    }

    /**
     * Redirections.
     *
     * @param array $toRedirect - Anciennes URL.
     * @param string $whereRedirect - URL vers où rediriger.
     * @param int $httpResponseCodeParam - Code de la réponse HTTP.
     */
    public function redirect(array $toRedirect, string $whereRedirect, int $httpResponseCodeParam = 301): void
    {
        if (in_array($this->router->getUri(), $toRedirect)) {
            Response::redirect(Helper::getBaseUrl().'/'.$whereRedirect, $httpResponseCodeParam);
        }
    }
}
