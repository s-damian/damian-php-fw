<?php

namespace DamianPhp\Hashing;

use DamianPhp\Support\Helper;
use DamianPhp\Contracts\Hashing\HashInterface;

/**
 * Classe client.
 * Gestion du hachage.
 *
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Hash implements HashInterface
{
    private static array $options = ['cost' => 10];

    /**
     * Créer une clé de hachage pour un password.
     */
    public function hash(string $password): bool|string
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, self::$options);

        if ($hash === false) {
            Helper::getExceptionOrLog('Bcrypt hashing not supported.');

            return false;
        }

        return $hash;
    }

    /**
     * Vérifier qu'un password correspond à un hachage.
     */
    public function verify(string $password, string $hashedPassword): bool
    {
        if (mb_strlen($hashedPassword) === 0) {
            return false;
        }

        return password_verify($password, $hashedPassword);
    }

    /**
     * Vérifiez si le hachage donné a été haché en utilisant les options données.
     */
    public function needsRehash(string $hashedValue): bool
    {
        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, self::$options);
    }
}
