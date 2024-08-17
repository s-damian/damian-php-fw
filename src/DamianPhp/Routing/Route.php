<?php

declare(strict_types=1);

namespace DamianPhp\Routing;

use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Log;

/**
 * Permet de représenter un route.
 *
 * # Fonctionnement :
 * - Vérifier si on a des params à Route.
 * - Dès qu'on a trouvé la correspondace du Path d'une Route avec URL actuelle :
 *   Instancier éventuel(s) middleware(s). Et initialiser un controller avec sa method, ou executer une function callable.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Route
{
    use RoutingTrait;

    public const REGEX_ID = '[0-9]+';

    public const REGEX_SLUG = '[0-9a-z\-]+';

    public const REGEX_KEY = '[0-9a-z]+';

    /**
     * Chemin d'URL.
     */
    private string $path;

    /**
     * Sera : controller@method, ou une closure.
     *
     * @var string|callable - Si string sera controller@method, si callable sera une closure.
     */
    private $callable;

    /**
     * Pour éventuellement ajouter middleware(s) dans routeGroup.
     */
    private ?string $middleware = null;

    /**
     * On va lui mettre les différentes correspondances.
     */
    private array $matches = [];

    /**
     * Pour le where().
     */
    private array $wheres = [];

    /**
     * Pour le pattern() (wheres seront prioritaires par rapport aux patterns).
     */
    private static array $patterns = [];

    /**
     * @param string|callable $callable
     */
    public function __construct(string $path, string|callable $callable, string $middleware = null)
    {
        $this->path = $path;
        $this->callable = $callable;

        if ($middleware !== null && $middleware !== '') {
            $this->middleware = $middleware;
        }
    }

    /**
     * Eventuellement stoker des params depuis boot() de App\Providers\RoutingServiceProvider
     *
     * where() sera prioritaire par rapport à pattern()
     *
     * @param string $param - Paramètre (exemple : id, slug...) à qui ajouter un regex.
     * @param string $regex - Expression régulière du param.
     */
    public static function pattern(string $param, string $regex): void
    {
        self::$patterns[$param] = str_replace('(', '(?:', $regex);
    }

    /**
     * Eventuellement stoker des params.
     * (str_replace, pour si il y a une parenthèse qui englobe regex, y remplacer par une parenthèse qui ne capture pas)
     *
     * @param string $param - Paramètre (ex. : id, slug...) à qui ajouter un regex.
     * @param string $regex - Expression régulière du param.
     */
    public function where(string $param, string $regex): self
    {
        $this->wheres[$param] = str_replace('(', '(?:', $regex);

        return $this;
    }

    /**
     * Oui ou Non, route matche.
     * Si Route correspond avec URL actuelle et REQUEST_METHOD actuelle.
     *
     * @param string $uri - $_SERVER['REQUEST_URI']
     */
    public function match(string $uri): bool
    {
        // Pour si params dans url (avec le where) -> Transformer url
        // Dans le path passé en param du constructeur :
        // Remplacer n'importe quelle carractères alpha-numérique ([\w]+) ...
        // ... soit par regex par defaut (n'importe quoi qui ne soit pas un slash) ....
        // ... ou si params -> le remplacer par sa regex spécifique (regex précisé dans ->where())
        $path = preg_replace_callback('#{([\w]+)}#', [$this, 'paramMatch'], $this->path);

        // Y trensformer en vrai expression régulière
        $regex = '#^'.$path.'$#i';
        // Verifier corespondance - Verifier si URL actuelle correspond à cette expression régulière
        if (! preg_match($regex, $uri, $matches)) {
            return false;
        }

        // Pour dégager index[0] pour récupérer les éventuels params.
        // (Ex. : pour si dans url "article/edit/23" ->
        // récupérer que "23" pour qu'on puisse l'envoyer en param de la méthode du controller qui va etre initialisé)
        array_shift($matches);

        $this->matches = $matches;

        return true;
    }

    /**
     * Si il y a un where pour autoriser seulement certains carractères -> retourne le regex passé en param du where.
     * Si non, si il y a un pattern pour autoriser seulement certains carractères -> retourne le regex passé en param du pattern.
     * Si non -> retourne regex par default (n'importe quoi qui ne soit pas un slash).
     */
    private function paramMatch(array $match): string
    {
        if (isset($this->wheres[$match[1]])) {
            return '('.$this->wheres[$match[1]].')';
        } elseif (isset(self::$patterns[$match[1]])) {
            return '('.self::$patterns[$match[1]].')';
        }

        return '([^/]+)';
    }

    /**
     * Si callable est un string -> Initialiser un controller avec sa method, et éventuellement lui envoyer les params (ex. : id, slug...).
     * Si callable est une closure -> Retourner partie callable, et éventuellement lui passer en params l'ensemble des correspondances des matches.
     */
    public function call(): mixed
    {
        $this->runMiddleware();

        if (is_string($this->callable)) {  // pour controller@action
            [$class, $action] = explode('@', $this->callable);

            $controllerClass = $this->getControllerWithNamespace($class);

            if (! class_exists($controllerClass)) {
                Helper::getExceptionOrGetError404('Class "'.$controllerClass.'" not found.');
            }

            $injectionsInConstruct = $this->getInjectionsInConstruct($controllerClass);

            $controller = new $controllerClass(...$injectionsInConstruct['for_construct']);

            if (! method_exists($controller, $action)) {
                Helper::getExceptionOrGetError404('Method "'.$action.'" not found in '.$controllerClass.'.');
            }

            return call_user_func_array([$controller, $action], $this->getParametersForCall($controller, $action, $injectionsInConstruct['for_action']));
        } elseif (is_callable($this->callable)) {  // pour closure
            return call_user_func_array($this->callable, $this->getParametersForCall());
        } else {
            Log::errorDamianPhp('Property "callable" of Routing must be a string or a callable in class in '.self::class.' on line '.__LINE__.'.');

            Helper::getExceptionOrGetError404('Property "callable" of Routing must be a string or a callable.');

            return false;
        }
    }

    /**
     * Si la route est dans un routeGroup -> Instancier éventuel(s) middleware(s).
     */
    private function runMiddleware(): void
    {
        if ($this->middleware !== null && $this->middleware !== '') {
            $middlewares = explode(',', $this->middleware);

            $mdw = [];
            foreach ($middlewares as $middleware) {
                if ($middleware !== '') {
                    $mdw[] = $middleware;
                }
            }

            $kernelClass = 'App\Http\Kernel';
            new $kernelClass($mdw);
        }
    }

    /**
     * Permet de récupérer les class qui sont injectées dans le constructeur.
     *
     * @return array
     * - 'for_construct' array - pour les classes à injécter dans constructeur.
     * - 'for_action' array (avec en keys les noms des classes injectés en param du construct) - pour les classes à injécter dans action.
     *    (le but est que si une classe en param de construct, et que cette meme classe en param de l'acion : injecter la même instance)
     */
    private function getInjectionsInConstruct(string $controllerClass): array
    {
        $reflectionClass = new ReflectionClass($controllerClass);

        $injectionsForConstruct = []; // on push les injections de methodes
        $injectionsForAction = []; // on push les injections de methodes
        // on parcours tous les paramètres de l'action ou de la closure
        foreach ($reflectionClass->getConstructor()->getParameters() as $parameter) {
            $classInjected = $parameter->getType() && ! $parameter->getType()->isBuiltin()
                ? new ReflectionClass($parameter->getType()->getName())
                : null;

            if ($classInjected !== null) { // si c'est une injection de méthode
                $injectionsForConstruct[] = new $classInjected->name(); // pour constructeur
                $injectionsForAction[$classInjected->name] = new $classInjected->name(); // pour action
            }
        }

        return ['for_construct' => $injectionsForConstruct, 'for_action' => $injectionsForAction];
    }

    /**
     * Reflection :
     * pour éventuellement utiliser l'injection de méthode dans l'action du controller,
     * ou pour éventuellement utiliser l'injection de méthode dans closure.
     */
    private function getParametersForCall(mixed $controller = null, string $action = null, array $injectionsInConstruct = []): array
    {
        if ($controller && $action) {
            // pour controller@action
            $reflectionFunction = new ReflectionMethod($controller, $action);
        } else {
            // pour closure
            $reflectionFunction = new ReflectionFunction($this->callable);
        }

        $injectionsWithMatches = []; // on fusionne les injections de methodes avec les matches de l'URL
        $i = 0; // pour qu'on puisse mettre dans n'importe quel ordre les matches et injections dans paramètres des function
        // on parcours tous les paramètres de l'action ou de la closure
        foreach ($reflectionFunction->getParameters() as $parameter) {
            $classInjected = $parameter->getType() && (method_exists($parameter->getType(), 'isBuiltin') && ! $parameter->getType()->isBuiltin())
                ? new ReflectionClass($parameter->getType()->getName())
                : null;

            if ($classInjected !== null) { // si c'est une injection de méthode
                if (isset($injectionsInConstruct[$classInjected->name])) { // si classe aussi en param du construct : injecter la même instance
                    $injectionsWithMatches[] = $injectionsInConstruct[$classInjected->name];
                } else {
                    $injectionsWithMatches[] = new $classInjected->name();
                }
            } else {
                if (isset($this->matches[$i])) { // pour si $reflectionFunction->getParameters() (action) a un paramètre qui est nullable
                    $injectionsWithMatches[] = $this->matches[$i];
                    $i++;
                }
            }
        }

        return $injectionsWithMatches;
    }

    /**
     * @param null|array|int|object|string $params - (OPTIONAL) Paramètre(s) de la route.
     * @return string - L'URL.
     */
    public function getUrl(null|array|int|object|string $params): string
    {
        $path = $this->path;

        if ($params) {
            $path = $this->getUrlPath($path, $params);
        }

        return $path;
    }

    private function getUrlPath($path, array|int|object|string $params): string
    {
        if (is_array($params)) {
            foreach ($params as $k => $v) {
                $path = str_replace('{'.$k.'}', (string) $v, $path);
            }
        } elseif (is_object($params)) {
            if (! property_exists($params, 'id')) {
                Helper::getExceptionOrLog('Property "id" not exists in class "'.$params.'".');
            }

            $path = str_replace('{id}', (string) $params->id, $path);
        } elseif (is_string($params) || is_int($params)) {
            $path = str_replace('{id}', (string) $params, $path);
        }

        return $path;
    }

    public function getCalledCallable(): string|callable
    {
        return $this->callable;
    }

    public function getCalledMiddlewares(): string
    {
        return $this->middleware;
    }
}
