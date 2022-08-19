<?php

namespace DamianPhp\Session;

use DamianPhp\Support\Facades\Response;
use DamianPhp\Contracts\Session\FlashInterface;

/**
 * Sessions flash.
 * Peut fonctionner avec une Facade.
 *
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Flash implements FlashInterface
{
    private Session $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * Pour si un message flash a été créé avec cette clé, l'afficher.
     *
     * @param $key - Clé du la session flash à afficher.
     */
    public function get(string $key): mixed
    {
        if ($this->session->has($key)) {
            $result = $this->session->get($key);

            $this->session->destroy($key);

            return $result;
        }

        return false;
    }

    /**
     * Pour messages de confirmation avec session.
     *
     * @param string $message - Message(s) d'info.
     */
    public function setSuccess(string $message): void
    {
        $this->session->put('_flash', Response::alertSuccess($message));
    }

    /**
     * Pour messages d'erreur(s) avec session flash.
     *
     * @param string $message - Message(s) d'info.
     */
    public function setError(string $message): void
    {
        $this->session->put('_flash', Response::alertError($message));
    }

    /**
     * Pour messages flashs d'info(s).
     */
    public function getResponse(): mixed
    {
        return $this->get('_flash');
    }
}
