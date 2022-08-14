<?php

namespace DamianPhp\Filesystem;

use DirectoryIterator;

/**
 * File management.
 * (this class can be used with a Facade)
 */
class File
{    
    /**
     * Ouvrir un dossier.
     */
    public static function open(string $path): DirectoryIterator
    {
        return new DirectoryIterator($path);
    }

    /**
     * Ouvre un dossier et renvoie ses éléments dans un tableau.
     */
    public static function openToArray(string $path): array
    {
        $files = [];

        foreach (self::open($path) as $item) {
            if (! $item->isDot()) {
                $files[] = $item->getFilename();
            }
        }

        return $files;
    }
    
    /**
     * Renommer un fichier ou un dossier.
     */
    public static function rename(string $oldName, string $newName): void
    {
        rename($oldName, $newName);
    }

    /**
     * Supprimer un fichier.
     */
    public static function destroy(string $file): void
    {
        unlink($file);
    }

    /**
     * Comptez le nombre de fichiers/dossiers dans un dossier.
     */
    public static function count($path): int
    {
        if ($path instanceof DirectoryIterator) {
            $forCount = $path;
        } else {
            $forCount = self::open($path);
        }

        return iterator_count($forCount) - 2;
    }

    /**
     * Créer un fichier.
     */
    public static function createFile(string $name): void
    {
        file_put_contents($name, null);
    }

    /**
     * Créer un dossier.
     */
    public static function createDir(string $name, int $mode = 0755): void
    {
        mkdir($name, $mode);
    }

    /**
     * Créer un/des dossier(s), si n'existe(nt) pas.
     */
    public static function createDirsIfNotExist(string $basePath, string $relativePath, int $mode = 0755): void
    {
        $subdirs = explode('/', $relativePath);

        $rDir = '';
        foreach ($subdirs as $subdir) {
            $rDir .= '/'.$subdir;

            if (!file_exists($basePath.$rDir)) {
                self::createDir($basePath.$rDir, $mode);
            }
        }
    }

    /**
     * Fonction récursive.
     * Supprimez le dossier et tout ce qu'il contient.
     */
    public static function destroyDirAndAllInside(string $listFilesFromPath): void
    {
        if (file_exists($listFilesFromPath) && is_dir($listFilesFromPath)) {
            if (mb_substr($listFilesFromPath, mb_strlen($listFilesFromPath) - 1, 1) !== '/') {
                $listFilesFromPath .= '/';
            }

            $files = glob($listFilesFromPath.'*', GLOB_MARK);

            foreach ($files as $file) {
                if (is_dir($file)) {
                    self::destroyDirAndAllInside($file);
                } elseif (is_file($file)) {
                    self::destroy($file);
                }
            }

            rmdir($listFilesFromPath);
        }
    }
}
