<?php

namespace DamianPhp\Config;

use DamianPhp\Support\Helper;
use DamianPhp\Contracts\Config\SingletonConfigInterface;

/**
 * Classe parent des classes de config.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
abstract class SingletonConfig implements SingletonConfigInterface
{
    /**
     * Pour charger les fichiers.
     *
     * @param string $method - Fichier à require (+ éventuellement keys).
     */
    abstract public function __call(string $method, array $arguments): mixed;

    /**
     * Singleton.
     */
    final public static function getInstance(): object
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * private - Car n'est pas autorisé à etre appelée de l'extérieur.
     */
    private function __construct()
    {
    }

    /**
     * private - Empêcher l'occurrence d'être cloné.
     */
    private function __clone()
    {
    }

    /**
     * Méthode réursive pour retrourner une valeur voulue selon une key (la key est la "valeur" après le dernier ".").
     *
     * @param array $requireFile - Résultat file chargé avec require_once
     * @param array $methodEx - Le path explosé (sans le file $methodEx[0])
     * @param string $key - Key sur laquelle récupérer la valeur (la key est la "valeur" après le dernier ".")
     * @param int $i - L'index du array.
     * @return mixed - Valeur si key existe, ou NULL si on appelle une key qui n'existe pas.
     */
    protected function exctactArrayFile(array $requireFile, array $methodEx, string $key, int $i): mixed
    {
        $i++;

        if (array_key_exists($methodEx[$i], $requireFile) && is_array($requireFile[$methodEx[$i]]) && $methodEx[$i] !== $key) {
            $result = $this->exctactArrayFile($requireFile[$methodEx[$i]], $methodEx, $key, $i);
        } else {
            if (! array_key_exists($key, $requireFile)) {
                Helper::getExceptionOrLog('Key "'.$key.'" not foud.');
            }

            $result = $requireFile[$key];
        }

        return $result;
    }
}
