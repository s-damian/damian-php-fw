<?php

namespace DamianPhp\Contracts\String;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface SlugInterface
{
    /**
     * Créer slug à parir d'une chaine de caractères.
     */
    public function create(string $str): string;

    /**
     * Si dans $this->str il y a carractère(s) qui n'existe(nt) pas dans les keys de $this->charactersArray() -> y remplacer par "-"
     */
    public function createKeywords(string $str): string;
}
