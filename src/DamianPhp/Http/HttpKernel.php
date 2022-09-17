<?php

namespace DamianPhp\Http;

use DamianPhp\Support\Helper;

/**
 * The HTTP Kernel.
 *
 * Cette classe est parent de App\Http\Middlewares\Kernel;
 * Exécuter le construct de cette classe à chaque instance de son enfant.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
abstract class HttpKernel
{
    /**
     * Key(s) passée(s) en param du Middleware dans Controller
     *
     * @var array|string
     */
    private array|string $key;

    /**
     * The application's route middleware.
     *
     * - To load a specific class and method:
     * 'key' => ['class to load', 'his method to execute'],
     *
     * - Pour charger une Class :
     * 'key' => 'class to load',
     */
    protected array $routeMiddleware = [
        'verify_csfr_token' => \App\Http\Middlewares\VerifyCsrfToken::class,

        // Admin :
        'admin_is_logged' => [\App\Http\Middlewares\Admin\IsLogged::class, 'isConnected'],
    ];

    public function __construct(array|string $key)
    {
        $this->key = $key;

        $this->verifyKeyExists();

        // Parcourir ensemble des routes du Kernel (class enfant)
        foreach ($this->routeMiddleware as $k => $v) {
            // Pour charger une Class et une Méthode spécifique
            if (is_array($v)) {
                $this->isArrayValueOfRouteMiddleware($k, $v);
            }
            // Pour charger une Class
            else {
                $this->isStringValueOfRouteMiddleware($k, $v);
            }
        }
    }

    /**
     * Verif si key(s) dans $this->middleware('key') existe(nt) dans les Routes du Middleware
     */
    private function verifyKeyExists(): void
    {
        if (Helper::config('app')['debug']) {
            // Pour si dans controller on fait : $this->middleware(['key', 'key2']);
            if (is_array($this->key)) {
                foreach ($this->key as $perKey) {
                    if (! array_key_exists($perKey, $this->routeMiddleware)) {
                        Helper::getExceptionOrLog('Key "'.$perKey.'" not exist in Routes of Middleware.');
                    }
                }
            }
            // Pour si dans controller on fait : $this->middleware('key');
            else {
                if (! array_key_exists($this->key, $this->routeMiddleware)) {
                    Helper::getExceptionOrLog('Key "'.$this->key.'" not exist in Routes of Middleware.');
                }
            }
        }
    }

    /**
     * Pour charger une Class et une Méthode spécifique.
     *
     * @param string $k - Tableaux numéroté - Les key des routes du Kernel.
     * @param array $v - Les value des routes du Kernel.
     */
    protected function isArrayValueOfRouteMiddleware(string $k, array $v): void
    {
        // Pour si dans controller on fait : $this->middleware(['key', 'key2']);
        if (is_array($this->key)) {
            foreach ($this->key as $perKey) {
                // si key de la route dans Kernel est égale à key passé dans Controller : $this->middleware('key')
                if ($perKey === $k) {
                    [$class, $method] = $v;

                    $class = new $class();
                    $class->$method();
                }
            }
        }
        // Pour si dans controller on fait : $this->middleware('key');
        else {
            // si key de la route dans Kernel est égale à key passé dans Controller : $this->middleware('key')
            if ($this->key === $k) {
                [$class, $method] = $v;

                $class = new $class();
                $class->$method();
            }
        }
    }

    /**
     * Pour charger une Class.
     *
     * @param string $k - Tableaux numéroté - Les key des routes du Kernel.
     * @param string $v - Les value des routes du Kernel.
     */
    private function isStringValueOfRouteMiddleware(string $k, string $v): void
    {
        // Pour si dans controller on fait : $this->middleware(['key', 'key2']);
        if (is_array($this->key)) {
            foreach ($this->key as $perKey) {
                // si key de la route dans Kernel est égale à key passé dans Controller : $this->middleware('key')
                if ($perKey === $k) {
                    new $v();
                }
            }
        }
        // Pour si dans controller on fait : $this->middleware('key');
        else {
            // si key de la route dans Kernel est égale à key passé dans Controller : $this->middleware('key')
            if ($this->key === $k) {
                new $v();
            }
        }
    }
}
