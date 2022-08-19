<?php

namespace DamianPhp\Form\Generators;

use DamianPhp\Support\Facades\Input;
use DamianPhp\Support\Facades\Request;
use DamianPhp\Support\Facades\Security;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
trait GeneratorTrait
{
    /**
     * @param string $type - Type de l'input.
     * @param string $name - Name de l'input.
     * @param string $value - Value de l'input.
     * @return string - Value de l'input ou valeur envoyé en POST.
     */
    private function getValueString(string $type, string $name, ?string $value): ?string
    {
        if (Request::isInMethods(['POST', 'PUT', 'PATCH']) && Input::hasPost($name) && $type !== 'password') {
            return Security::e(Input::post($name));
        }

        return $value;
    }

    private function getRequired(array $options): string
    {
        return isset($options['required']) && $options['required'] === true ? ' required ' : '';
    }
}
