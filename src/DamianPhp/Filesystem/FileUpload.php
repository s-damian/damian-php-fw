<?php

namespace DamianPhp\Filesystem;

use DamianPhp\Support\Facades\Input;
use DamianPhp\Contracts\Filesystem\FileInterface;

/**
 * Classe client.
 * Gestion des uploads de fichiers.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class FileUpload implements FileInterface
{
    /**
     * @var string $path - Dossier ciblé.
     */
    private string $path;

    /**
     * @var null|string $input - Attribut html name="" de l'input.
     */
    private ?string $input;

    /**
     * @var string - Eventuel préfix du fichier uploadé.
     */
    private string $prefixName;

    /**
     * @var string|array - Nom de(s) fichier(s) qui vien(nen)t d'être uploadé(s).
     */
    private $name;

    /**
     * @param string $path - Dossier ciblé.
     * @param string|null $input - Attribut html name="" de l'input.
     * @param array|null $options - Eventuelles options.
     * - $options['prefix'] string
     */
    public function __construct(string $path, string $input = null, array $options = [])
    {
        $this->path = $path;
        $this->input = $input;
        $this->prefixName = $options['prefix'] ?? '';
    }

    /**
     * Uploader le fichier.
     *
     * @return bool|int - True si le fichier a bien été uploadé. Si c'est un array : retourne le nombre de fihiers uploadés.
     */
    public function move(): bool|int
    {
        $fileName = Input::file($this->input)['name'];

        if (is_array($fileName)) {
            return $this->uploadMultiple($fileName);
        } else {
            return $this->upload($fileName);
        }
    }

    /**
     * @param array $fileName
     * @return bool|int - Retourne le nombre de fichier(s) uploadé(s), ou retourne false si $i reste à 0.
     */
    private function uploadMultiple(array $fileName): bool|int
    {
        $i = 0;

        foreach ($fileName as $key => $oneFileName) {
            $oneName = $this->prefixName.$oneFileName;

            $fileTmpName = Input::file($this->input)['tmp_name'][$key];
            $pathMove = $this->path.'/'.$oneFileName;

            if (move_uploaded_file($fileTmpName, $pathMove)) {
                $this->name[$oneName] = $oneName;
                $i++;
            }
        }

        return $i > 0 ? $i : false;
    }

    /**
     * @return bool - True si le fichier a bien été uploadé.
     */
    private function upload(string $fileName): bool
    {
        $this->name = $this->prefixName.$fileName;

        $fileTmpName = Input::file($this->input)['tmp_name'];
        $pathMove = $this->path.'/'.$this->name;

        return move_uploaded_file($fileTmpName, $pathMove);
    }

    /**
     * @return array|string - Nom du/des fichié(s) uploadé(s).
     */
    public function getName(): array|string
    {
        return $this->name;
    }

    /**
     * Supprimer le fichier.
     *
     * @return bool - True si le fichier a bien été supprimé.
     */
    public function remove(string $name): bool
    {
        $fileToVerif = $this->path.'/'.$name;

        if (is_file($fileToVerif) && file_exists($fileToVerif)) {
            unlink($fileToVerif);

            return true;
        }

        return false;
    }
}
