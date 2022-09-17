<?php

namespace DamianPhp\Support;

use DamianPhp\Config\Lang;
use DamianPhp\Config\Config;
use DamianPhp\Filesystem\File;
use DamianPhp\Support\Facades\Router;
use DamianPhp\Support\Facades\Server;
use DamianPhp\Exception\ExceptionHandler;

/**
 * Helpers.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Helper
{
    /**
     * Obtient la valeur d'une variable d'environnement.
     */
    public static function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? false;

        if ($value === false) {
            return $default;
        }

        switch (mb_strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        return $value;
    }

    /**
     * Path du dossier racine public.
     *
     * @param string|null $file
     * @return string
     */
    public static function publicPath(string $file = null): string
    {
        $pathPrefix = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));

        if ($file) {
            return $pathPrefix.'/'.Helper::config('path')['public'].'/'.$file;
        }

        return $pathPrefix.'/'.Helper::config('path')['public'];
    }

    /**
     * Path du dossier racine qui contient toute l'application.
     */
    public static function basePath(string $file = null): string
    {
        $pathPrefix = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));

        if ($file) {
            return $pathPrefix.'/'.$file;
        }

        return $pathPrefix;
    }

    /**
     * Path du dossier de storage.
     */
    public static function storagePath(string $file = null): string
    {
        $slash = $file !== null ? '/' : '';

        if (! file_exists(self::basePath('storage'))) {
            File::createDir(self::basePath('storage'), 0755);
        }

        return self::basePath('storage'.$slash.$file);
    }

    /**
     * Charger une vue.
     */
    public static function load(string $file, array $data = []): void
    {
        if ($data) {
            extract($data);
        }

        require_once self::basePath('resources/views/'.$file.'.php');
    }

    /**
     * @return string - Pour liens href internes en absolues.
     */
    public static function getBaseUrl(): string
    {
        $test = Server::getRequestScheme().'://'.Server::getHttpHost();

        return $test;
    }

    /**
     * @return string - URL en absolue (avec le nom de la route, et éventuel(s) param(s)).
     */
    public static function route(string $path, mixed $params = null): string
    {
        if (Router::url($path, $params) === '') {
            return self::getBaseUrl(); // pour ne pas avoir le slash en fin d'URL pour la homepage
        }

        return self::getBaseUrl().'/'.Router::url($path, $params);
    }

    /**
     * @return string - URL active en absolue.
     */
    public static function getActiveUrl(): string
    {
        return Server::getRequestScheme().'://'.Server::getHttpHost().Server::getRequestUri();
    }

    /**
     * @return bool - True si on est en local.
     */
    public static function isLocalServer(): bool
    {
        return env('APP_ENV') === 'local';
    }

    /**
     * @return bool - True si on est sur un serveur de preprod.
     */
    public static function isPreprodServer(): bool
    {
        return env('APP_ENV') === 'preprod';
    }

    /**
     * @return bool - True si l'IP request est dans les IP de développement.
     */
    public static function isDevIP(): bool
    {
        return in_array(Server::getIp(), self::config('dev')['dev_ip']);
    }

    /**
     * @return mixed - Helper de config.
     */
    public static function config(string $method): mixed
    {
        return Config::getInstance()->$method();
    }

    /**
     * Utilse pour les translations spécifiques au framework ("core").
     *
     * @return mixed - Helper de la translation.
     */
    public static function lang(string $method): mixed
    {
        return Lang::getInstance()->$method();
    }

    /**
     * @return bool - True si l'internationalisation est activé.
     */
    public static function isMultilingual(): bool
    {
        return self::config('lang')['internationalization'] === true;
    }

    /**
     * @return bool - True si la lange locale est = à lang testée.
     */
    public static function isLocale(string $lang): bool
    {
        return self::lang('getLocale') === $lang;
    }

    /**
     * @return string - Langue (soit celle par default, soit celle choisie par le visiteur) sous la forme 'fr'.
     */
    public static function getLocale(): string
    {
        return self::lang('getLocale');
    }

    /**
     * Renvoyer une exception avec un message d'erreur si on est en dev.
     */
    public static function getException(string $message): void
    {
        $e = new ExceptionHandler();
        $e->getException($message);
    }

    /**
     * Si on est en dev : Renvoyer une exception avec un message d'erreur.
     * Si on est en prod : Logger l'erreur.
     *
     * @throws ExceptionHandler
     */
    public static function getExceptionOrLog(string $message): void
    {
        $e = new ExceptionHandler();
        $e->getExceptionOrLog($message);
    }

    /**
     * Si on est en dev : Renvoyer une exception avec un message d'erreur.
     * Si on est en prod : Error 404.
     */
    public static function getExceptionOrGetError404(string $message): void
    {
        $e = new ExceptionHandler();
        $e->getExceptionOrGetError404($message);
    }

    /**
     * Renvoyer une erreur HTTP 404.
     */
    public static function getError404(): void
    {
        $e = new ExceptionHandler();
        $e->getError404();
    }
}
