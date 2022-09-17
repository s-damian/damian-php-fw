<?php

namespace DamianPhp\Routing;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Log;
use DamianPhp\Support\Facades\Str;
use DamianPhp\Support\Facades\Server;
use DamianPhp\Support\Facades\Request;
use DamianPhp\Contracts\Routing\RouterInterface;

/**
 * Classe client.
 * Router de l'appliction.
 * Peut fonctionner avec une Facade
 *
 * # Fonctionnement :
 * - Eventuellemet ajouter des routeGroups (pour éventuellemnt ajouter prefix, middleware(s), et namespace)
 * - Détécter les routes avec leurs méthods (GET, POST...), et les ajouter à $this->routes avec des objets de la classe Route
 * - Parcourir toutes les routes selon la REQUEST_METHOD actuelle
 * - Dès qu'on a trouvé la correspondace du Path d'une Route avec URL actuelle -> Initialiser un controller avec sa method, ou executer une function callable
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Router implements RouterInterface
{
    use RoutingTrait;

    /**
     * URI (sans params).
     */
    private string $uri;

    /**
     * Pour l'éventuelle internationalisation.
     */
    private LangParsing $langParsing;

    /**
     * Pour l'éventuelle internationalisation.
     */
    private Redirector $redirector;

    /**
     * Pour éventuellement ajouter des routeGroups (des prefixs).
     */
    private ?string $prefix = null;

    /**
     * Pour éventuellement ajouter un namespace à controller.
     */
    private string $namespace = '';

    /**
     * Pour éventuellement ajouter middleware(s) dans routeGroup.
     */
    private ?string $middleware = null;

    /**
     * Toutes les routes dans un array assoiatifs d'objets (avec en keys les méthods (GET, POST...)).
     *
     * @var array - Tableaux assoiatifs.
     */
    private array $routes = [];

    /**
     * Pour si la route est nommée.
     *
     * @var array - Tableaux assoiatifs.
     */
    private array $namedRoute = [];

    /**
     * private - Car n'est pas autorisé à etre appelée de l'extérieur.
     */
    public function __construct()
    {
        $this->setUri();

        $this->langParsing = new LangParsing($this);

        $this->redirector = new Redirector($this);
    }

    /**
     * Parser l'URL.
     */
    private function setUri()
    {
        if (Str::contains(Server::getUri(), '?')) {
            $ex = explode('?', Server::getUri());
            $uri = $ex[0];
        } elseif (Str::contains(Server::getUri(), '&')) {
            $ex = explode('&', Server::getUri());
            $uri = $ex[0];
        } else {
            $uri = Server::getUri();
        }

        $this->uri = $uri;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Eventuellement stoker des params depuis RoutingServiceProvider.
     *
     * where() sera prioritaire par rapport à pattern().
     *
     * @param string $param - Paramètre (ex. : id, slug...) à qui ajouter un regex.
     * @param string $regex - Expression régulière du param.
     */
    public static function pattern(string $param, string $regex): void
    {
        Route::pattern($param, $regex);
    }

    /**
     * Pour éventuellement ajouter des routeGroups - éventuellemnt ajouter prefix, middleware(s), et namespace à route.
     *
     * @param array $args
     * - $args['prefix'] string - éventuel préfix.
     * - $args['namespace'] string - éventuel namespace.
     * - $args['middleware'] string|array - éventuel middleware(s).
     * @param callable $callable
     */
    public function group(array $args, callable $callable): void
    {
        $this->verifyKeysInGroupOptions($args);

        $this->setGroupArgs($args);

        $callable();

        $this->cleanGroupArgs($args);
    }

    /**
     * Eventuellement ajouter prefix, middleware(s), et namespace à route.
     */
    private function setGroupArgs(array $args): void
    {
        if (isset($args['prefix'])) {
            $this->prefix .= $args['prefix'];
        }

        if (isset($args['namespace'])) {
            $this->namespace .= $args['namespace'];
        }

        if (isset($args['middleware'])) {
            if (is_array($args['middleware'])) {
                foreach ($args['middleware'] as $argMiddleware) {
                    $this->middleware .= $argMiddleware.',';
                }
            } else {
                $this->middleware .= $args['middleware'].',';
            }
        }
    }

    /**
     * Néttoyer les éventuels prefix, middleware(s), et namespace.
     */
    private function cleanGroupArgs(array $args): void
    {
        if (isset($args['prefix'])) {
            $this->prefix = mb_substr($this->prefix, 0, mb_strlen($this->prefix) - mb_strlen($args['prefix']));
        }

        if (isset($args['namespace'])) {
            $this->namespace = mb_substr($this->namespace, 0, mb_strlen($this->namespace) - mb_strlen($args['namespace']));
        }

        if (isset($args['middleware'])) {
            if (is_array($args['middleware'])) {
                foreach ($args['middleware'] as $argMiddleware) {
                    $this->middleware = mb_substr($this->middleware, 0, mb_strlen($this->middleware) - mb_strlen($argMiddleware.','));
                }
            } else {
                $this->middleware = mb_substr($this->middleware, 0, mb_strlen($this->middleware) - mb_strlen($args['middleware'].','));
            }
        }
    }

    /**
     * Vérifier que les keys écrites dans routeGroupe sont bonnes.
     */
    private function verifyKeysInGroupOptions(array $args): void
    {
        if (Helper::config('app')['debug']) {
            foreach ($args as $key => $arg) {
                if (! in_array($key, ['prefix', 'middleware', 'namespace'])) {
                    Helper::getExceptionOrLog('Key not good.');
                }
            }
        }
    }

    /**
     * Pour ajouter plusieurs routes grace à resource :
     *
     * @param string $controller - Controller avec lequel faire les routes.
     * @param array - OPTIONAL array associatif $options - Eventuel options (préfix au nom de la route, except).
     * - $options['prefix_name'] string - préfix aux noms des route.
     * - $options['except'] array - Sauf ces routes.
     * - $options['only'] array - Seulement ces routes.
     */
    public function resource(string $controller, array $options = []): void
    {
        $resourceRegistrar = new ResourceRegistrar($this);

        $resourceRegistrar->resource($controller, $options);
    }

    /**
     * Ajouter routes avec toutes les methods.
     *
     * @param string|callable $callable
     */
    public function any(string $path, string|callable $callable, array $args = []): void
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'];

        $this->match($methods, $path, $callable, $args);
    }

    /**
     * Ajouter route avec methodd choisies.
     *
     * @param array $methods - Listes des methods choisies.
     * @param string|callable $callable
     */
    public function match(array $methods, string $path, string|callable $callable, array $args = []): void
    {
        foreach ($methods as $method) {
            $this->$method($path, $callable, $args);
        }
    }

    /**
     * Ajouter une route avec la méthode GET.
     * Lire.
     *
     * @param string|callable $callable
     */
    public function get(string $path, string|callable $callable, array $args = []): Route
    {
        $routeGet = $this->addRoute($path, $callable, 'GET', $args);

        $this->head($path, $callable);

        return $routeGet;
    }

    /**
     * Ajouter une route avec la méthode HEAD.
     * Lire (entête seulement).
     *
     * @param string|callable $callable
     */
    public function head(string $path, string|callable $callable, array $args = []): Route
    {
        return $this->addRoute($path, $callable, 'HEAD', $args);
    }

    /**
     * Ajouter une route avec la méthode POST.
     * Créer.
     *
     * @param string|callable $callable
     */
    public function post(string $path, string|callable $callable, array $args = []): Route
    {
        return $this->addRoute($path, $callable, 'POST', $args);
    }

    /**
     * Ajouter une route avec la méthode PUT.
     * Mettre à jour.
     *
     * @param string|callable $callable
     */
    public function put(string $path, string|callable $callable, array $args = []): Route
    {
        return $this->addRoute($path, $callable, 'PUT', $args);
    }

    /**
     * Ajouter une route avec la méthode PATCH.
     * Partiellement mettre à jour.
     *
     * @param string|callable $callable
     */
    public function patch(string $path, string|callable $callable, array $args = []): Route
    {
        return $this->addRoute($path, $callable, 'PATCH', $args);
    }

    /**
     * Ajouter une route avec la méthode DELETE.
     * Supprimer.
     *
     * @param string|callable $callable
     */
    public function delete(string $path, string|callable $callable, array $args = []): Route
    {
        return $this->addRoute($path, $callable, 'DELETE', $args);
    }

    /**
     * Ajouter une route avec la méthode OPTIONS.
     * Toutes les methods HTTP + d'autres option.
     *
     * @param string|callable $callable
     */
    public function options(string $path, string|callable $callable, array $args = []): Route
    {
        return $this->addRoute($path, $callable, 'OPTIONS', $args);
    }

    /**
     * Ajouter une route (avec get() ou post()...).
     *
     * @param string $path - Chemin d'url.
     * @param string|callable $callable - controller@method, ou closure avec function anonyme.
     * @param string $method - Méthode HTTP (GET, POST, etc.).
     * @param array|null $args - OPTIONAL.
     * - $args['name'] string - Pour éventuellement nommer la route.
     * @return Route
     */
    private function addRoute(string $path, string|callable $callable, string $method, array $args = []): Route
    {
        $pathForRoute = $this->langParsing->getLangForAddPathRoute().$this->prefix.$path;

        $callableForRoute = is_callable($callable) ? $callable : $this->namespace.$callable;

        $route = new Route($pathForRoute, $callableForRoute, $this->middleware);
        $this->routes[$method][] = $route;

        if (isset($args['name'])) {
            if (! isset($this->namedRoute[$args['name']])) { // si Nom n'est pas déjà pris par une autre Route
                $this->namedRoute[$args['name']] = $route;
            } else {
                Helper::getExceptionOrLog('The name "'.$args['name'].'" is already taken by other route.');
            }
        }

        return $route;
    }

    /**
     * Toutes les routes.
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Executer rooting.
     * Parcourir ensemble des routes selon la REQUEST_METHOD actuelle.
     * Si ça match (Path de la Route correspond avec URL actuelle) -> appeler callable.
     *
     * @return mixed - Callable (Action ou Closure) si ça match. Si ne match pas, return false.
     * @throws ExceptionHandler
     */
    public function dispatch(): mixed
    {
        $this->verifyRequestMethodHasRoutes();

        $this->redirector->verifyUrlBeforeAttemptMatching();

        foreach ($this->routes[Request::getMethod()] as $route) {
            if ($route->match($this->uri)) {
                return $route->call();
            }
        }

        $this->redirector->lastVerifyUrlBeforeGetErrorMatching();

        Helper::getExceptionOrGetError404('No matching routes for URI "'.$this->uri.'" with method "'.Request::getMethod().'".');

        return false;
    }

    /**
     * Verifier que la méthode HTTP a une (ou des) route(s).
     */
    private function verifyRequestMethodHasRoutes(): void
    {
        if (! isset($this->routes[Request::getMethod()])) {
            Log::errorDamianPhp('Method "'.Request::getMethod().'" does not exist for URI "'.$this->uri.'" in '.get_class().' on line '.__LINE__.'.');

            Helper::getExceptionOrGetError404('Method "'.Request::getMethod().'" does not exist for URI "'.$this->uri.'".');
        }
    }

    /**
     * Pour préciser dans la liste des routes les URL où on veut faire de l'internolisation.
     */
    public function trans(bool $bool): void
    {
        $this->langParsing->trans($bool);
    }

    /**
     * - Si lang 'default' n'est pas dans 'languages_allowed' -> erreur.
     * - Vérifier que l'internationalisation est activé, et que l'URL GET "testé" est bien dans 'languages_allowed'.
     *
     * @return string - L'éventuelle langue (celle de l'URL sous la forme 'fr', ou celle par defaut).
     */
    public function getLang(): string
    {
        return $this->langParsing->getLang();
    }

    /**
     * Eventuellement modifier langue par défaut (avec session, cookie, ou géolocalisation par exemple).
     * Que lorsque lang n'est pas précisé dans URL que ça redirige vers nouvelle lang par defaut.
     */
    public function setDefaultLang(string $lang): void
    {
        $this->langParsing->setDefaultLang($lang);
    }

    /**
     * Afficher url avec le nom d'un route.
     *
     * @param string $name - Nom de la route.
     * @param null|array|object $params - Paramètre(s) de la route.
     * @throws ExceptionHandler
     * @return string - URL.
     */
    public function url(string $name, $params = null): string
    {
        if (! isset($this->namedRoute[$name])) {
            Helper::getExceptionOrLog('No route have this name "'.$name.'".');
        }

        return $this->namedRoute[$name]->getUrl($params);
    }

    public function calledCallable(string $name): string|callable
    {
        return $this->namedRoute[$name]->getCalledCallable();
    }

    public function calledMiddlewares(string $name): string
    {
        return $this->namedRoute[$name]->getCalledMiddlewares();
    }

    /**
     * Pour éxécuter une action finale.
     *
     * @param string $controllerAndAction - Nom du controller et nom de la méthod.
     * @param null|array $arguments - Eventuels paramètres à envoyer.
     */
    public function getAction(string $controllerAndAction, array $arguments = []): mixed
    {
        [$class, $action] = explode('@', $controllerAndAction);

        $controllerClass = $this->getControllerWithNamespace($class);

        if (! class_exists($controllerClass)) {
            Helper::getExceptionOrGetError404('Class "'.$controllerClass.'" not found.');
        }

        $controller = new $controllerClass();

        if (! method_exists($controller, $action)) {
            Helper::getExceptionOrGetError404('Method "'.$action.'" not found in '.$controllerClass.'.');
        }

        return call_user_func_array([$controller, $action], $arguments);
    }

    /**
     * Redirections.
     *
     * @param array $toRedirect - Anciennes URL.
     * @param string $whereRedirect - URL vers où rediriger.
     * @param int $httpResponseCodeParam - Code de la réponse HTTP.
     */
    public function redirect(array $toRedirect, string $whereRedirect, int $httpResponseCodeParam = 301): void
    {
        $this->redirector->redirect($toRedirect, $whereRedirect, $httpResponseCodeParam);
    }
}
