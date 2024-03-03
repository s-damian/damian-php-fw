<?php

declare(strict_types=1);

namespace DamianPhp\Http\Request;

use DamianPhp\Contracts\Http\Request\InputInterface;

/**
 * Pour traitements des formulaires, et GET...
 * Peut fonctionner avec une Facade.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Input implements InputInterface
{
    private Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    public function hasPost(string $name): bool
    {
        return $this->request->getPost()->has($name);
    }

    public function post(string $name): mixed
    {
        return $this->request->getPost()->get($name);
    }

    public function hasGet(string $name): bool
    {
        return $this->request->getGet()->has($name);
        ;
    }

    public function get(string $name): mixed
    {
        return $this->request->getGet()->get($name);
    }

    public function hasFile(string $name): bool
    {
        return $this->request->getFiles()->has($name);
    }

    /**
     * Si donnée est envoyée en FILE, et si ce $name existe -> return $_FILE['name'].
     */
    public function file(string $name): mixed
    {
        return $this->request->getFiles()->get($name);
    }
}
