<?php

namespace DamianPhp\Http\Request\Bags;

/**
 * Ce trait est inclut dans les classes : ParameterBag, ServerBag
 *
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
trait BagTrait
{
    /**
     * @return array - Les paramètres.
     */
    public function all(): array
    {
        return $this->parameters;
    }

    /**
     * @return array - Les paramètres des keys.
     */
    public function keys(): array
    {
        return array_keys($this->parameters);
    }

    /**
     * @return int - Le nombre de paramètres.
     */
    public function count(): int
    {
        return count($this->parameters);
    }

    /**
     * @return bool - True si le paramètre existe.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->parameters);
    }

    /**
     * @return mixed - Retourne un paramètre par son nom.
     */
    public function get(string $key, mixed $default = ''): mixed
    {
        return $this->has($key) ? $this->parameters[$key] : $default;
    }
}
