<?php

use DamianPhp\Support\Helper;

/**
 * Helpers du Framework.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */

if (! function_exists('env')) {
    /**
     * Obtient la valeur d'une variable d'environnement.
     */
    function env(string $key, mixed $default = null): mixed
    {
        return Helper::env($key, $default);
    }
}

if (! function_exists('publicPath')) {
    /**
     * Path du dossier racine public.
     */
    function publicPath(string $file = null): string
    {
        return Helper::publicPath($file);
    }
}

if (! function_exists('basePath')) {
    /**
     * Path du dossier racine qui contient toute l'application.
     */
    function basePath(string $file = null): string
    {
        return Helper::basePath($file);
    }
}

if (! function_exists('storagePath')) {
    /**
     * Path du dossier de storage.
     */
    function storagePath(string $file = null)
    {
        return Helper::storagePath($file);
    }
}

if (! function_exists('load')) {
    /**
     * Charger une vue.
     */
    function load(string $file, array $data = []): void
    {
        Helper::load($file, $data);
    }
}

if (! function_exists('getBaseUrl')) {
    /**
     * @return string - Pour liens href internes en absolues.
     */
    function getBaseUrl(): string
    {
        return Helper::getBaseUrl();
    }
}

if (! function_exists('route')) {
    /**
     * @return string - URL en absolue (avec le nom de la route, et éventuel(s) param(s)).
     */
    function route(string $path, mixed $params = null): string
    {
        return Helper::route($path, $params);
    }
}

if (! function_exists('getActiveUrl')) {
    /**
     * @return string - URL active en absolue.
     */
    function getActiveUrl(): string
    {
        return Helper::getActiveUrl();
    }
}

if (! function_exists('isLocalServer')) {
    /**
     * @return bool - True si on est en local.
     */
    function isLocalServer(): bool
    {
        return Helper::isLocalServer();
    }
}

if (! function_exists('isPreprodServer')) {
    /**
     * @return bool - True si on est sur un serveur de preprod.
     */
    function isPreprodServer(): bool
    {
        return Helper::isPreprodServer();
    }
}

if (! function_exists('isDevIP')) {
    /**
     * @return bool - True si l'IP request est dans les IP de développement.
     */
    function isDevIP(): bool
    {
        return Helper::isDevIP();
    }
}

if (! function_exists('config')) {
    /**
     * @return mixed - Helper de config.
     */
    function config(string $method): mixed
    {
        return Helper::config($method);
    }
}

if (! function_exists('lang')) {
    /**
     * Utilse pour les translations spécifiques au framework ("core").
     *
     * @return mixed - Helper de la translation.
     */
    function lang(string $method): mixed
    {
        return Helper::lang($method);
    }
}

if (! function_exists('isMultilingual')) {
    /**
     * @return bool - True si l'internationalisation est activé.
     */
    function isMultilingual(): bool
    {
        return Helper::isMultilingual();
    }
}

if (! function_exists('isLocale')) {
    /**
     * @return bool - True si la lange locale est = à lang testée.
     */
    function isLocale(string $lang): bool
    {
        return Helper::isLocale($lang);
    }
}

if (! function_exists('getLocale')) {
    /**
     * @return string - Langue (soit celle par default, soit celle choisie par le visiteur) sous la forme 'fr'.
     */
    function getLocale(): string
    {
        return Helper::getLocale();
    }
}

if (! function_exists('getException')) {
    /**
     * Renvoyer une exception avec un message d'erreur si on est en dev.
     */
    function getException(string $message): void
    {
        Helper::getException($message);
    }
}

if (! function_exists('getExceptionOrLog')) {
    /**
     * Si on est en dev : Renvoyer une exception avec un message d'erreur.
     * Si on est en prod : Logger l'erreur.
     *
     * @throws ExceptionHandler
     */
    function getExceptionOrLog(string $message): void
    {
        Helper::getExceptionOrLog($message);
    }
}

if (! function_exists('getExceptionOrGetError404')) {
    /**
     * Si on est en dev : Renvoyer une exception avec un message d'erreur.
     * Si on est en prod : Error 404.
     */
    function getExceptionOrGetError404(string $message): void
    {
        Helper::getExceptionOrGetError404($message);
    }
}

if (! function_exists('getError404')) {
    /**
     * Renvoyer une erreur HTTP 404.
     */
    function getError404(): void
    {
        Helper::getError404();
    }
}
