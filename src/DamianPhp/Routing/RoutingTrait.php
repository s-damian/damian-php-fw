<?php

declare(strict_types=1);

namespace DamianPhp\Routing;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
trait RoutingTrait
{
    private function getControllerWithNamespace(string $class): string
    {
        // pour ne pas etre obligé de mettre 'Controller' en fin de controller
        if (mb_substr($class, -mb_strlen('Controller'), mb_strlen('Controller'))  === 'Controller') {
            $classController = $class;
        } else {
            $classController = $class.'Controller';
        }

        // pour ne pas etre obligé de mettre namespace 'App\Http\Controllers\\' avant Controller
        if (mb_substr($classController, 0, mb_strlen('App\Http\Controllers\\')) === 'App\Http\Controllers\\') {
            $controllerClass = $classController;
        } else {
            $controllerClass = 'App\Http\Controllers\\'.$classController;
        }

        return $controllerClass;
    }
}
