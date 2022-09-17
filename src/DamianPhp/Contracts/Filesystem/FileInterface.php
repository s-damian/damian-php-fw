<?php

namespace DamianPhp\Contracts\Filesystem;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface FileInterface
{
    /**
     * @param string $path - Dossier ciblé.
     * @param string|null $input - Attribut html name="" de l'input.
     * @param array|null $options - Eventuelles options.
     * - $options['prefix'] (string)
     */
    public function __construct(string $path, string $input = null, array $options = []);

    /**
     * Uploader le fichier.
     *
     * @return bool|int - True si le fichier a bien été uploadé. Si c'est un array : retourne le nombre de fihiers uploadés.
     */
    public function move(): bool|int;

    /**
     * @return array|string - Nom du/des fichié(s) uploadé(s).
     */
    public function getName(): array|string;

    /**
     * Supprimer le fichier.
     *
     * @return bool - True si le fichier a bien été supprimé.
     */
    public function remove(string $name): bool;
}
