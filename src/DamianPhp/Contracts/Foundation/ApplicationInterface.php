<?php

declare(strict_types=1);

namespace DamianPhp\Contracts\Foundation;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface ApplicationInterface
{
    /**
     * Démmarer $_SESSION
     */
    public function startSession(): void;

    /**
     * Pour gérer les erreurs et les logs d'erreurs PHP.
     */
    public function ifError(): void;

    /**
     * Charger tous les Services Providers.
     */
    public function initProviders(): void;

    /**
     * Eventuellement interdire certaines adresses IP d'accès au site.
     */
    public function ifIpIsForbidden(): mixed;

    /**
     * Eventuellement mettre le site web en maintenance.
     */
    public function ifIsMaintenance(): mixed;

    /**
     * Cahrger la liste des routes et exécuter le Routing.
     */
    public function run(): void;
}
