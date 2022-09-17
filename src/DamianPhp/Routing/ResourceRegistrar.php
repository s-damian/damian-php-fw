<?php

namespace DamianPhp\Routing;

use DamianPhp\Support\Helper;
use DamianPhp\Contracts\Routing\RouterInterface;

/**
 * Gestion des resource du Router.
 *
 * @index GET/HEAD     : '/resource'
 * @create GET/HEAD    : '/resource/create'
 *   @store POST       : '/resource/create'
 * @show GET/HEAD      : '/resource/{id}'
 * @edit GET/HEAD      : '/resource/{id}/edit'
 *   @update PUT/PATCH : '/resource/{id}/edit'
 * @destroy DELETE     : '/resource/{id}/destroy'
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class ResourceRegistrar
{
    /**
     * L'instance router.
     */
    private RouterInterface $router;

    /**
     * Path des routes de resource.
     */
    private array $pathResource = [];

    /**
     * Toutes les actions que peut avoir une resource.
     */
    private const RESOURCE_ACTIONS = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];

    /**
     *  ResourceRegistrarconstructor.
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;

        $this->pathResource = Helper::config('routing')['resource_path'];
    }

    /**
     * Pour ajouter plusieurs routes grace à resource :
     *
     * @param string $controller - Controller avec lequel faire les routes.
     * @param array - (OPTIONAL) array associatif $options - Eventuel options (préfix au nom de la route, except).
     * - $options['prefix_name'] : string - Préfix aux noms des route.
     * - $options['except'] : array - Sauf ces routes.
     * - $options['only'] : array - Seulement ces routes.
     */
    public function resource(string $controller, array $options = []): void
    {
        $this->verifyOptions($options);

        $prefixRouteName = $options['prefix_name'] ?? '';

        foreach (self::RESOURCE_ACTIONS as $action) {
            $method = 'addResource'.ucfirst($action);

            if (! isset($options['except']) && !isset($options['only'])) {
                $this->$method($controller, $prefixRouteName);
            } elseif (isset($options['except']) && !in_array($action, $options['except'])) {
                $this->$method($controller, $prefixRouteName);
            } elseif (isset($options['only']) && in_array($action, $options['only'])) {
                $this->$method($controller, $prefixRouteName);
            }
        }
    }

    private function verifyOptions(array $options): void
    {
        if (isset($options['except']) && isset($options['only'])) {
            Helper::getExceptionOrLog('Options "except" and "only" not can isset simultaneously.');
        }

        $this->verifyExceptAndVerifyOnly($options, 'except')->verifyExceptAndVerifyOnly($options, 'only');
    }

    private function verifyExceptAndVerifyOnly(array $options, string $verify): self
    {
        if (isset($options[$verify])) {
            foreach ($options[$verify] as $verif) {
                if (! in_array($verif, self::RESOURCE_ACTIONS)) {
                    Helper::getExceptionOrLog(ucfirst($verify).' "'.$verif.'" is not a resource action.');
                }
            }
        }

        return $this;
    }

    /**
     * @param string $controller - Controller avec lequel faire la route.
     * @param string $prefixRouteName - Eventuel prérfix au nom de la route.
     */
    private function addResourceIndex(string $controller, string $prefixRouteName): void
    {
        $this->router
            ->get($this->pathResource['index'], $controller.'@index', ['name' => $prefixRouteName.'index']);
    }

    /**
     * @param string $controller - Controller avec lequel faire la route.
     * @param string $prefixRouteName - Eventuel prérfix au nom de la route.
     */
    private function addResourceCreate(string $controller, string $prefixRouteName): void
    {
        $this->router
            ->get('/'.$this->pathResource['create'], $controller.'@create', ['name' => $prefixRouteName.'create']);
    }

    /**
     * @param string $controller - Controller avec lequel faire la route.
     * @param string $prefixRouteName - Eventuel prérfix au nom de la route.
     */
    private function addResourceStore(string $controller, string $prefixRouteName): void
    {
        $this->router
            ->post('/'.$this->pathResource['create'], $controller.'@store');
    }

    /**
     * @param string $controller - Controller avec lequel faire la route.
     * @param string $prefixRouteName - Eventuel prérfix au nom de la route.
     */
    private function addResourceShow(string $controller, string $prefixRouteName): void
    {
        $this->router
            ->get('/{id}'.$this->pathResource['show'], $controller.'@show', ['name' => $prefixRouteName.'show'])
            ->where('id', Route::REGEX_ID);
    }

    /**
     * @param string $controller - Controller avec lequel faire la route.
     * @param string $prefixRouteName - Eventuel prérfix au nom de la route.
     */
    private function addResourceEdit(string $controller, string $prefixRouteName): void
    {
        $this->router
            ->get('/{id}/'.$this->pathResource['edit'], $controller.'@edit', ['name' => $prefixRouteName.'edit'])
            ->where('id', Route::REGEX_ID);
    }

    /**
     * @param string $controller - Controller avec lequel faire la route.
     * @param string $prefixRouteName - Eventuel prérfix au nom de la route.
     */
    private function addResourceUpdate(string $controller, string $prefixRouteName): void
    {
        $this->router
            ->put('/{id}/'.$this->pathResource['edit'], $controller.'@update')
            ->where('id', Route::REGEX_ID);

        $this->router
            ->patch('/{id}/'.$this->pathResource['edit'], $controller.'@update')
            ->where('id', Route::REGEX_ID);
    }

    /**
     * @param string $controller - Controller avec lequel faire la route.
     * @param string $prefixRouteName - Eventuel prérfix au nom de la route.
     */
    private function addResourceDestroy(string $controller, string $prefixRouteName): void
    {
        $this->router
            ->delete('/{id}/'.$this->pathResource['destroy'], $controller.'@destroy')
            ->where('id', Route::REGEX_ID);
    }
}
