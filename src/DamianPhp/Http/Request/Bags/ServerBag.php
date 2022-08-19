<?php

namespace DamianPhp\Http\Request\Bags;

/**
 * Bag des : $_SERVER
 *
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class ServerBag
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
}
