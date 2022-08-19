<?php

namespace DamianPhp\Contracts\Cache;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface CacheInterface
{
    public function __construct();

    /**
     * Créer un fichier cache.
     */
    public function put(string $file, mixed $value): mixed;

    /**
     * Récupérer la valeur du fichier de cache sérialisé sous forme d'objet
     *
     * @param null|int $minutes - Eventuellement définir une durée de vie au fichier (pour le mettre à jour).
     */
    public function get(string $file, int $minutes = null): mixed;

    /**
     * Récupérer la valeur du fichier de cache sérialisé sous forme d'objet
     *
     * @param string $file
     * @param int|null $minutes - Eventuellement définir une durée de vie au fichier (pour le mettre à jour)
     * @return mixed
     */
    public function getToObject(string $file, int $minutes = null): mixed;


    /**
     * Récupérer la valeur du fichier de cache sérialisé sous forme d'array.
     *
     * @param int|null $minutes - Eventuellement définir une durée de vie au fichier (pour le mettre à jour).
     */
    public function getToArray(string $file, int $minutes = null): mixed;

    /**
     * Récupérer la valeur du fichier de cache, ou le créer si n'existe pas.
     *
     * @param int $minutes - Eventuellement définir une durée de vie au fichier (pour le mettre à jour).
     */
    public function remember(string $file, int $minutes, callable $callable): mixed;

    /**
     * Savoir si un fichier de cache existe.
     */
    public function has(string $file): bool;

    /**
     * Supprimer un fichier de cache.
     */
    public function destroy(string $file): void;

    /**
     * Vider tout le dossier cache et tous les fichiers (et uniquement les fichiers) qui sont à l'intérieur.
     *
     * @param string $directory - Eventuel dossier (pour supprimer que un dossier spécifique).
     */
    public function clear(string $directory = null): void;
}
