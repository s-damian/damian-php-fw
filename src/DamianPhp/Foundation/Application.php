<?php

namespace DamianPhp\Foundation;

use DamianPhp\Support\Helper;
use DamianPhp\Filesystem\File;
use DamianPhp\Support\Facades\Router;
use DamianPhp\Support\Facades\Server;
use DamianPhp\Support\Facades\Session;
use DamianPhp\Contracts\Foundation\ApplicationInterface;

/**
 * Pour créer l'application.
 *
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Application implements ApplicationInterface
{
    /**
     * Démmarer $_SESSION
     */
    public function startSession(): void
    {
        ini_set('session.cookie_domain', Helper::config('cookie')['domain']);
        ini_set('session.save_path', Helper::storagePath('sessions'));

        if (! file_exists(Helper::storagePath('sessions'))) {
            File::createDir(Helper::storagePath('sessions'), 0755);
        }

        Session::start();
    }

    /**
     * Pour gérer les erreurs et les logs d'erreurs PHP.
     */
    public function ifError(): void
    {
        error_reporting(E_ALL);

        if (Helper::config('app')['debug']) {
            ini_set('display_errors', '1');
        } else {
            ini_set('display_errors', '0');

            ini_set('log_errors', '1');

            $logFilePath = Helper::storagePath('logs/errors-php.log');
            if (file_exists($logFilePath)) {
                ini_set('error_log', $logFilePath);
            }
        }
    }

    /**
     * Charger tout les Services Providers.
     */
    public function initProviders(): void
    {
        $providers = Helper::config('boot')['providers'];

        foreach ($providers as $provider) {
            $providerInstance = new $provider();

            if (method_exists($providerInstance, 'boot')) {
                $providerInstance->boot();
            }
        }
    }

    /**
     * Eventuellement interdire certaines adresses IP d'accès au site.
     */
    public function ifIpIsForbidden(): mixed
    {
        if (in_array(Server::getIp(), Helper::config('app')['ip_forbidden'])) {
            return Router::getAction('App\Http\Controllers\Error\ErrorController@error403');
        }

        return true;
    }

    /**
     * Eventuellement mettre le site web en maintenance.
     */
    public function ifIsMaintenance(): mixed
    {
        if (Helper::config('app')['maintenance']) {
            if (! Helper::isDevIP()) {
                return Router::getAction('App\Http\Controllers\Error\ErrorController@error503');
            }
        }

        return true;
    }

    /**
     * Charger la liste des routes et exécuter le Routing.
     */
    public function run(): void
    {
        Router::dispatch();
    }
}
