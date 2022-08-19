<?php

namespace DamianPhp\Contracts\Http\Request;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface InputInterface
{
    public function __construct();

    public function hasPost(string $name): bool;

    public function post(string $name): mixed;

    public function hasGet(string $name): bool;

    public function get(string $name): mixed;

    public function hasFile(string $name): bool;

    /**
     * Si donnée est envoyée en FILE, et si ce $name existe -> return $_FILE['name'].
     */
    public function file(string $name): mixed;
}
