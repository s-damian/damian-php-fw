<?php

namespace DamianPhp\Http\Request\Bags;

/**
 * Bag des : $_FILES
 *
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class FileBag
{
    /**
     * Files storage.
     */
    private array $files;

    public function __construct(array $files = [])
    {
        $this->files = $files;
    }

    /**
     * @return mixed - Retourne un paramètre by name.
     */
    public function get(string $key): mixed
    {
        return $this->has($key) ? $this->files[$key] : '';
    }

    /**
     * @return bool - True si le paramètre existe.
     */
    public function has(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['name'] !== '';
    }
}
