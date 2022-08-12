<?php

namespace DamianPhp\Contracts\Security;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface TokenInterface
{
    /**
     * Si pas de session token -> lui ajouter
     */
    public function addSession();

    /**
     * A mettre dans HTML des form en POST
     * class="token-post" - pour ajax, contre faill CSRF
     *
     * @return string
     */
    public function htmlPost();

    /**
     * A mettre dans traitements des form en POST
     */
    public function verifyPost();

    /**
     * A mettre dans liens en GET
     *
     * @param string $getOrAnd
     * @return string
     */
    public function htmlGet(string $getOrAnd = '?');

    /**
     * A mettre dans traitements des form en GET
     */
    public function verifyGet();
}
