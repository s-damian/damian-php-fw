<?php

namespace DamianPhp\Exception;

use Exception;
use DamianPhp\Mail\Mailer;
use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Log;
use DamianPhp\Support\Facades\Router;
use DamianPhp\Support\Facades\Server;
use DamianPhp\Contracts\Exception\ExceptionHandlerInterface;

/**
 * Gestionnaire des Exceptions 
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class ExceptionHandler extends Exception implements ExceptionHandlerInterface
{
    /**
     * Renvoyer une exception avec un message d'erreur (si le debug est activé).
     */
    public function getException(string $message): void
    {
        if (Helper::config('app')['debug']) {
            $this->runException($message);
        }
    }

    /**
     * Si le debug est activé : Renvoyer une exception avec un message d'erreur.
     * Si non : Logger l'erreur.
     */
    public function getExceptionOrLog(string $message): void
    {
        if (Helper::config('app')['debug']) {
            $this->runException($message);
        } else {
            Log::errorDamianPhp('Exception in '.get_class().' on line '.__LINE__.': '.$message);
        }
    }

    /**
     * Envoyer une Exception et éventuellement envoyer un mail.
     */
    private function runException(string $message)
    {
        if (Helper::config('email')['send_mail_if_exception'] && !Helper::config('app')['debug']) {
            $mailer = new Mailer();
            $mailer->setFrom(Helper::config('email')['email_error_from'])
                ->setTo(Helper::config('email')['email_error_to'])
                ->setSubject('Exception - '.Server::getServerName())
                ->setBody('errors/exception-html', ['message' => $message])
                ->addBodyText('errors/exception-text', ['message' => $message])
                ->send();
        }

        throw new self('Exception: '.$message);
    }

    /**
     * Si on est en dev : Renvoyer une exception avec un message d'erreur.
     * Si on est en prod : Error 404.
     */
    public function getExceptionOrGetError404(string $message): void
    {
        if (Helper::config('app')['debug']) {
            throw new self('Error 404: '.$message);
        } else {
            Helper::getError404();
        }
    }

    /**
     * Retourne l'action d'erreur 404.
     */
    public function getError404(): mixed
    {
        return Router::getAction('App\Http\Controllers\Error\ErrorController@error404');
    }
}
