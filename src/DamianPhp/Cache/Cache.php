<?php

declare(strict_types=1);

namespace DamianPhp\Cache;

use DamianPhp\Support\Helper;
use DamianPhp\Filesystem\File;
use DamianPhp\Contracts\Cache\CacheInterface;

/**
 * Classe client.
 * Pour faire de la mise en cache.
 * Peut fonctionner avec une Facade.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Cache implements CacheInterface
{
    /**
     * Dossier dans lequel on va stocker la mise en cache.
     */
    private string $direname;

    public function __construct()
    {
        if (! file_exists(Helper::storagePath('cache'))) {
            File::createDir(Helper::storagePath('cache'));
        }

        $this->direname = Helper::storagePath('cache');
    }

    /**
     * Créer un fichier cache.
     */
    public function put(string $file, mixed $value): mixed
    {
        if (is_object($value) || is_array($value)) {
            $array = [];

            foreach ($value as $key => $value) {
                $array[$key] =  $value;
            }

            $content = serialize($array);
        } else {
            $content = $value;
        }

        return file_put_contents($this->direname.'/'.$file, $content);
    }

    /**
     * Récupérer la valeur du fichier de cache.
     *
     * @param null|int $minutes - Eventuellement définir une durée de vie au fichier (pour le mettre à jour).
     */
    public function get(string $file, ?int $minutes = null): mixed
    {
        if (! $this->has($file)) {
            return false;
        }

        if ($minutes !== null) {
            // date du jour - (moins) date de création du fichier
            $lifeTime = (time() - filemtime($this->direname.'/'.$file)) / 60;

            // si durrée de vie du fichier en cache est > à la durrée définie
            if ($lifeTime > $minutes) {
                $this->destroy($file);

                return false;
            }
        }

        return file_get_contents($this->direname.'/'.$file);
    }

    /**
     * Récupérer la valeur du fichier de cache sérialisé sous forme d'objet.
     *
     * @param null|int $minutes - Eventuellement définir une durée de vie au fichier (pour le mettre à jour).
     */
    public function getToObject(string $file, ?int $minutes = null): mixed
    {
        $value = $this->get($file, $minutes);

        if ($value !== false) {
            return (object) unserialize($value);
        }

        return false;
    }

    /**
     * Récupérer la valeur du fichier de cache sérialisé sous forme d'array.
     *
     * @param int|null $minutes - Eventuellement définir une durée de vie au fichier (pour le mettre à jour).
     */
    public function getToArray(string $file, ?int $minutes = null): mixed
    {
        $value = $this->get($file, $minutes);

        if ($value !== false) {
            return unserialize($value);
        }

        return false;
    }

    /**
     * Récupérer la valeur du fichier de cache, ou le créer si n'existe pas.
     *
     * @param int $minutes - Eventuellement définir une durée de vie au fichier (pour le mettre à jour).
     */
    public function remember(string $file, int $minutes, callable $callable): mixed
    {
        if (! $toReturn = $this->getToObject($file, $minutes)) {
            $toReturn = $callable();

            $this->put($file, $toReturn);
        }

        return $toReturn;
    }

    /**
     * Savoir si un fichier de cache existe.
     */
    public function has(string $file): bool
    {
        return file_exists($this->direname.'/'.$file);
    }

    /**
     * Supprimer un fichier de cache.
     */
    public function destroy(string $file): void
    {
        if ($this->has($file)) {
            unlink($this->direname.'/'.$file);
        }
    }

    /**
     * Vider tout le dossier cache et tous les fichiers (et uniquement les fichiers) qui sont à l'intérieur.
     *
     * @param string $directory - Eventuel dossier (pour supprimer que un dossier spécifique).
     */
    public function clear(?string $directory = null): void
    {
        $pathToFiles = ($directory !== null) ? $this->direname.'/'.$directory : $this->direname;
        $files = glob($pathToFiles.'/*', GLOB_MARK);

        foreach ($files as $file) {
            if (file_exists($file) && is_dir($file)) {
                if (mb_substr($file, 0, mb_strlen($this->direname)) === $this->direname) {
                    $file = mb_substr($file, mb_strlen($this->direname));
                }

                $this->clear($file);
            } elseif (file_exists($file) && is_file($file)) {
                unlink($file);
            }
        }
    }
}
