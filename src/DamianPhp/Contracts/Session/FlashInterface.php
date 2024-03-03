<?php

declare(strict_types=1);

namespace DamianPhp\Contracts\Session;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface FlashInterface
{
    public function __construct();

    /**
     * Pour si un message flash a été créé avec cette clé, l'afficher.
     *
     * @param $key - Clé du la session flash à afficher.
     */
    public function get(string $key);

    /**
     * Pour messages de confirmation avec session.
     *
     * @param string $message - Message(s) d'info.
     */
    public function setSuccess(string $message);

    /**
     * Pour messages d'erreur(s) avec session flash.
     *
     * @param string $message - Message(s) d'info.
     */
    public function setError(string $message);

    /**
     * Pour messages flashs d'info(s).
     */
    public function getResponse();
}
