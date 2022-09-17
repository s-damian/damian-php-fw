<?php

namespace DamianPhp\Config;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Str;

/**
 * Pour require les fichiers qui sont dans "/config/".
 * Cette classe doit fonctionner uniquement avec singleton.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
final class Config extends SingletonConfig
{
    protected static ?self $instance = null;

    /**
     * @var array - Pour charger qu'une seule fois un fichier.
     */
    private static array $require = [];

    /**
     * Pour charger les fichiers de config (un fichier est chargé qu'une seule fois).
     *
     * @param string $method - Fichier à require (+ éventuellement des keys).
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
     * Pour utiliser ce format : config('config-file.key.key2');
     *
     * self::$require[$file] - aura le contenu du fichier.
     * $keyToExtractValue - sera la clé de la valeur à récupérer.
     *
     * @param string $method - Fichier à require.
     */
    private function withCallWithPoints(string $method): mixed
    {
        $methodEx = explode('.', $method);

        $file = $methodEx[0];
        unset($methodEx[0]);

        if (! isset(self::$require[$file])) {
            $path = Helper::basePath('config/'.$file.'.php');

            if (file_exists($path)) {
                self::$require[$file] = require_once $path;
            } else {
                Helper::getExceptionOrLog('Config File "'.$path.'" not exists.');
            }
        }

        $keyToExtractValue = end($methodEx);

        // utile pour ces formats : config('email.array.key_existe') ou : config('email.key_inexistante')
        // mais pas utile pour ce format : config('file.key_existe')
        if (! isset(self::$require[$file][$keyToExtractValue])) {
            self::$require[$file][$keyToExtractValue] = $this->exctactArrayFile(self::$require[$file], $methodEx, $keyToExtractValue, 0);
        }

        return self::$require[$file][$keyToExtractValue];
    }

    /**
     * Pour utiliser ce format : config('config-file')['key']['key2'];
     *
     * @param string $method - Fichier à require.
     */
    private function withCallWithoutPoints(string $method): mixed
    {
        if (! isset(self::$require[$method])) {
            $path = Helper::basePath('config/'.$method.'.php');

            if (file_exists($path)) {
                self::$require[$method] = require_once $path;
            } else {
                Helper::getExceptionOrLog('Config File "'.$path.'" not exists.');
            }
        }

        return self::$require[$method];
    }
}
