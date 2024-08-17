<?php

declare(strict_types=1);

namespace DamianPhp\Http\Request\Bags;

/**
 * Bag des : $_GET, $_POST, $_COOKIE
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class ParameterBag
{
    use BagTrait;

    /**
     * Parameter storage.
     */
    private array $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * Modifier le paramètre par son nom.
     */
    public function set(string $key, mixed $value): void
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Supprimer un paramètre
     *
     * @param string $key - Le name
     */
    public function destroy(string $key): void
    {
        unset($this->parameters[$key]);
    }

    /**
     * Supprimer tous les paramètres
     */
    public function clear(): void
    {
        $this->parameters = [];
    }
}
