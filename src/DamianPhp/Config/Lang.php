<?php

declare(strict_types=1);

namespace DamianPhp\Config;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Str;
use DamianPhp\Support\Facades\Router;

/**
 * Pour les langues. Et require les fichiers qui sont dans "/resources/lang/".
 * Cette classe doit fonctionner uniquement avec singleton.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Lang extends SingletonConfig
{
    protected static ?self $instance = null;

    /**
     * @var array - Pour charger qu'une seule fois un fichier.
     */
    private static array $require = [];

    /**
     * Pour charger les fichiers de lang (un fichier est chargé qu'une seule fois).
     *
     * @param string $method - Fichier à require.
     */
    public function __call(string $method, array $arguments): mixed
    {
        if (Str::contains($method, '.')) {
            return $this->withCallWithPoints($method);
        } else {
            return $this->withCallWithoutPoints($method);
        }
    }

    /**
     * Pour utiliser ce format : lang('lang-file.key.key2');
     *
     * @param string $method - Fichier à require.
     */
    private function withCallWithPoints(string $method): mixed
    {
        $methodEx = explode('.', $method);

        $file = $methodEx[0];
        unset($methodEx[0]);

        if (! isset(self::$require[$file])) {
            $path = Helper::basePath('resources/lang/'.$this->getLocale().'/'.$file.'.php');

            if (file_exists($path)) {
                self::$require[$file] = require_once $path;
            } else {
                Helper::getExceptionOrLog('Lang File "'.$path.'" not exists.');
            }
        }

        $keyToExtractValue = end($methodEx);

        if (! isset(self::$require[$file][$keyToExtractValue])) {
            self::$require[$file][$keyToExtractValue] = $this->exctactArrayFile(self::$require[$file], $methodEx, $keyToExtractValue, 0);
        }

        return self::$require[$file][$keyToExtractValue];
    }

    /**
     * Pour utiliser ce format : echo lang('lang-file')['key']['key2'];
     *
     * @param string $method - Fichier à require.
     */
    private function withCallWithoutPoints(string $method): mixed
    {
        if (! isset(self::$require[$method])) {
            $path = Helper::basePath('resources/lang/'.$this->getLocale().'/'.$method.'.php');

            if (file_exists($path)) {
                self::$require[$method] = require_once $path;
            } else {
                Helper::getExceptionOrLog('Lang File "'.$path.'" not exists.');
            }
        }

        return self::$require[$method];
    }

    /**
     * @return string - Langue (soit celle par default, soit celle choisie par le visiteur) sous la forme 'fr'.
     */
    public function getLocale(): string
    {
        static $lang;

        if ($lang === null) {
            if (Helper::isMultilingual()) {
                $lang = Router::getLang();
            } else {
                $lang = Helper::config('lang')['default'];
            }

            if (! in_array($lang, Helper::config('lang')['languages_allowed'])) {
                Helper::getExceptionOrLog('Lang "'.$lang.'" is not in "languages_allowed".');
            }
        }

        return $lang;
    }
}
