<?php

declare(strict_types=1);

namespace DamianPhp\Contracts\Log;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface LogInterface
{
    public function __construct();

    /**
     * Envoyer un log d'information de l'app (pour les fichiers qui sont dans le dossier "app").
     *
     * @param string $file - Pour éventuellement y logger dans un fichier spécifique.
     */
    public function infoApp(string $message, string $file = 'default/infos'): void;

    /**
     * Envoyer un log d'erreur de l'app (pour les fichiers qui sont dans le dossier "app").
     *
     * @param string $file - Pour éventuellement y logger dans un fichier spécifique.
     */
    public function errorApp(string $message, string $file = 'default/errors'): void;

    /**
     * Envoyer un log d'erreur du framework (pour les fichiers qui sont dans le dossier "core").
     */
    public function errorDamianPhp(string $message): void;
}
