<?php

namespace DamianPhp\Contracts\Routing;

use DamianPhp\Routing\Route;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface RouterInterface
{
    /**
     * private - Car n'est pas autorisé à etre appelée de l'extérieur.
     */
    public function __construct();

    public function getUri(): string;

    /**
     * Eventuellement stoker des params depuis RoutingServiceProvider.
     *
     * where() sera prioritaire par rapport à pattern().
     *
     * @param string $param - Paramètre (ex. : id, slug...) à qui ajouter un regex.
     * @param string $regex - Expression régulière du param.
     */
    public static function pattern(string $param, string $regex): void;

    /**
     * Pour éventuellement ajouter des routeGroups - éventuellemnt ajouter prefix, middleware(s), et namespace à route.
     *
     * @param array $args
     * - $args['prefix'] string - éventuel préfix.
     * - $args['middleware'] string|array - éventuel middleware(s).
     * - $args['namespace'] string - éventuel namespace.
     * @param callable $callable
     */
    public function group(array $args, callable $callable): void;

    /**
     * Pour ajouter plusieurs routes grace à resource :
     *
     * @param string $controller - Controller avec lequel faire les routes.
     * @param array - OPTIONAL array associatif $options - Eventuel options (préfix au nom de la route, except).
     * - $options['prefix_name'] string - Préfix aux noms des route.
     * - $options['except'] array - Sauf ces routes.
     * - $options['only'] array - Seulement ces routes.
     */
    public function resource(string $controller, array $options = []): void;

    /**
     * Ajouter routes avec toutes les methods.
     *
     * @param string|callable $callable
     */
    public function any(string $path, string|callable $callable, array $args = []): void;

    /**
     * Ajouter route avec methodd choisies.
     *
     * @param array $methods - Listes des methods choisies.
     * @param string|callable $callable
     */
    public function match(array $methods, string $path, string|callable $callable, array $args = []): void;

    /**
     * Ajouter route avec method GET.
     * Lire.
     *
     * @param string|callable $callable
     */
    public function get(string $path, string|callable $callable, array $args = []): Route;

    /**
     * Ajouter route avec method HEAD.
     * Lire (entête seulement).
     *
     * @param string|callable $callable
     */
    public function head(string $path, string|callable $callable, array $args = []): Route;

    /**
     * Ajouter route avec method POST.
     * Créer.
     *
     * @param string|callable $callable
     */
    public function post(string $path, string|callable $callable, array $args = []): Route;

    /**
     * Ajouter route avec method PUT.
     * Mettre à jour.
     *
     * @param string|callable $callable
     */
    public function put(string $path, string|callable $callable, array $args = []): Route;

    /**
     * Ajouter route avec method PATCH.
     * Partiellement mettre à jour.
     *
     * @param string|callable $callable
     */
    public function patch(string $path, string|callable $callable, array $args = []): Route;

    /**
     * Ajouter route avec method DELETE.
     * Supprimer.
     *
     * @param string|callable $callable
     */
    public function delete(string $path, string|callable $callable, array $args = []): Route;

    /**
     * Ajouter route avec method OPTIONS.
     * Toutes les methods HTTP + d'autres option.
     *
     * @param string|callable $callable
     */
    public function options(string $path, string|callable $callable, array $args = []): Route;

    /**
     * Toutes les routes.
     */
    public function getRoutes(): array;

    /**
     * Executer rooting.
     * Parcourir ensemble des routes selon la REQUEST_METHOD actuelle.
     * Si ça match (Path de la Route correspond avec URL actuelle) -> appeler callable.
     *
     * @return mixed - Callable (Action ou Closure) si ça match. Si ne match pas, return false.
     * @throws ExceptionHandler
     */
    public function dispatch(): mixed;

    /**
     * Pour préciser dans la liste des routes les URL où on veut faire de l'internolisation.
     */
    public function trans(bool $bool): void;

    /**
     * - Si lang 'default' n'est pas dans 'languages_allowed' -> erreur.
     * - Vérifier que l'internationalisation est activé, et que l'URL GET "testé" est bien dans 'languages_allowed'.
     *
     * @return string - L'éventuelle langue (celle de l'URL sous la forme 'fr', ou celle par defaut).
     */
    public function getLang(): string;

    /**
     * Eventuellement modifier langue par défaut (avec session, cookie, ou géolocalisation par exemple).
     * Que lorsque lang n'est pas précisé dans URL que ça redirige vers nouvelle lang par defaut.
     */
    public function setDefaultLang(string $lang): void;

    /**
     * Afficher url avec le nom d'un route.
     *
     * @param string $name - Nom de la route.
     * @param null|array|object $params - Paramètre(s) de la route.
     * @throws ExceptionHandler
     * @return string - URL.
     */
    public function url(string $name, $params = null): string;

    /**
     * Pour éxécuter une action finale.
     *
     * @param string $controllerAndAction - Nom du controller et nom de la méthod.
     * @param null|array $arguments - Eventuels paramètres à envoyer.
     */
    public function getAction(string $controllerAndMethod): mixed;

    /**
     * Redirections.
     *
     * @param array $toRedirect - Anciennes URL.
     * @param string $whereRedirect - URL vers où rediriger.
     * @param int $httpResponseCodeParam - Code de la réponse HTTP.
     */
    public function redirect(array $toRedirect, string $whereRedirect, int $httpResponseCodeParam = 301): void;
}
