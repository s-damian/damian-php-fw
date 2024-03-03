<?php

declare(strict_types=1);

namespace DamianPhp\Http\Response;

use DamianPhp\Support\Helper;

/**
 * JSON.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Json
{
    /**
     * Modifier le contenu d'un fichier JSON.
     *
     * @return bool - True si le fichier a bien été modifié.
     */
    public function set(string $file, mixed $value): bool
    {
        if (file_exists($file)) {
            if (file_put_contents($file, json_encode($value))) {
                return true;
            }
        }

        Helper::getExceptionOrLog('An error occurred while modifying the JSON file.');

        return false;
    }

    /**
     * Récupérer le contenu d'un fichier JSON.
     *
     * @param $assoc - True = response sous form d'array associatif. False = response sous form d'object.
     */
    public function get(string $file, bool $assoc = false): mixed
    {
        $jsonFile = file_get_contents($file);

        return json_decode($jsonFile, $assoc);
    }

    /**
     * Encoder au format JSON.
     */
    public function encode(mixed $value): string
    {
        return json_encode($value);
    }

    /**
     * Décoder du JSON.
     */
    public function decode(mixed $value): mixed
    {
        return json_decode($value);
    }
}
