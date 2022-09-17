<?php

namespace DamianPhp\Http\Request;

use DamianPhp\Support\Facades\Str;
use DamianPhp\Http\Request\Bags\FileBag;
use DamianPhp\Http\Request\Bags\ServerBag;
use DamianPhp\Http\Request\Bags\ParameterBag;
use DamianPhp\Support\Facades\Server as ServerF;
use DamianPhp\Contracts\Http\Request\RequestInterface;

/**
 * Request.
 * Peut fonctionner avec une Facade.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Request implements RequestInterface
{
    /**
     * @var ParameterBag - $_GET
     */
    private ParameterBag $get;

    /**
     * @var ParameterBag - $_POST
     */
    private ParameterBag $post;

    /**
     * @var ParameterBag - $_COOKIE
     */
    private ParameterBag $cookies;

    /**
     * @var ServerBag - $_SERVER
     */
    private ServerBag $server;

    /**
     * @var FileBag - $_FILES
     */
    private FileBag $files;

    public function __construct()
    {
        $this->get = new ParameterBag($_GET);
        $this->post = new ParameterBag($_POST);
        $this->cookies = new ParameterBag($_COOKIE);
        $this->server = new ServerBag($_SERVER);
        $this->files = new FileBag($_FILES);
    }

    public function __get(string $name)
    {
        if ($this->isGet()) {
            if ($this->get->has($name)) {
                return $this->get->get($name);
            }
        } elseif ($this->isInMethods(['POST', 'PUT', 'PATCH'])) {
            if ($this->post->has($name)) {
                return $this->post->get($name);
            }
        }

        return;
    }

    public function getPost(): ParameterBag
    {
        return $this->post;
    }

    public function getGet(): ParameterBag
    {
        return $this->get;
    }

    public function getCookies(): ParameterBag
    {
        return $this->cookies;
    }

    public function getServer(): ServerBag
    {
        return $this->server;
    }

    public function getFiles(): FileBag
    {
        return $this->files;
    }

    /**
     * @param string $method - Méthode passé en paramètre.
     * @return bool - True si request méthode est égal à la méthode passée en paramètre.
     */
    public function isMethod(string $method): bool
    {
        $methodHttp = mb_strtoupper($method);

        if ($methodHttp === 'AJAX') {
            return $this->isAjax();
        } elseif ($methodHttp === 'CLI') {
            return $this->isCli();
        }

        return $this->getMethod() === $methodHttp;
    }

    /**
     * @param array $methods - Méthodes passées en paramètre.
     * @return bool - True si request méthode est égal à une des méthodes passées en paramètre.
     */
    public function isInMethods(array $methods): bool
    {
        foreach ($methods as $method) {
            if ($this->isMethod($method) === true) {
                return true;
            }
        }

        return false;
    }

    public function isGet(): bool
    {
        return $this->isMethod('GET');
    }

    public function isPost(): bool
    {
        return $this->isMethod('POST');
    }

    public function isPut(): bool
    {
        return $this->isMethod('PUT');
    }

    public function isDelete(): bool
    {
        return $this->isMethod('DELETE');
    }

    public function isPatch(): bool
    {
        return $this->isMethod('PATCH');
    }

    public function isHead(): bool
    {
        return $this->isMethod('HEAD');
    }

    public function isOptions(): bool
    {
        return $this->isMethod('OPTIONS');
    }

    public function isAjax(): bool
    {
        return $this->server->has('HTTP_X_REQUESTED_WITH') && $this->server->get('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest';
    }

    public function isCli(): bool
    {
        return php_sapi_name() === 'cli';
    }

    public function getMethodsAllowedForInputMethod(): array
    {
        return ['PUT', 'PATCH', 'DELETE'];
    }

    /**
     * @return string - Méthode utilisée pour accéder à la page : 'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD' ou 'OPTIONS'.
     */
    public function getMethod(): string
    {
        if ($this->post->has('_method')) {
            $methodInput = mb_strtoupper($this->post->get('_method'));

            if (in_array($methodInput, $this->getMethodsAllowedForInputMethod())) {
                return $methodInput;
            }
        }

        return $this->getRequestMethod();
    }

    /**
     * @return string - Méthode HTTP utilisée pour accéder à la page : 'GET' ou 'POST'.
     */
    public function getRequestMethod(): string
    {
        return ServerF::getMethod();
    }

    /**
     * @return string - L'URL courante (sans les éventuels query params).
     */
    public function getUrlCurrent(): string
    {
        $requestUri = ServerF::getRequestUri();

        if (Str::contains($requestUri, '?')) {
            $ex = explode('?', $requestUri);
            $uri = $ex[0];
        } elseif (Str::contains($requestUri, '&')) {
            $ex = explode('&', $requestUri);
            $uri = $ex[0];
        } else {
            $uri = $requestUri;
        }

        return ServerF::getRequestScheme().'://'.ServerF::getServerName().$uri;
    }

    public function getFullUrlWithQuery(array $query)
    {
        $question = '?';

        return self::getUrlCurrent().$question.$this->buildQuery(array_merge(self::getGet()->all(), $query));
    }

    public function buildQuery($array)
    {
        return http_build_query($array, '', '&', PHP_QUERY_RFC3986);
    }
}
