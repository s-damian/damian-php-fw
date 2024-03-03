<?php

declare(strict_types=1);

namespace DamianPhp\Contracts\Hashing;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface HashInterface
{
    /**
     * Créer une clé de hachage pour un password.
     */
    public function hash(string $password): bool|string;

    /**
     * Vérifier qu'un password correspond à un hachage.
     */
    public function verify(string $password, string $hashedPassword): bool;

    /**
     * Vérifiez si le hachage donné a été haché en utilisant les options données.
     */
    public function needsRehash(string $hashedValue): bool;
}
