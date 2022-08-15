<?php

namespace DamianPhp\Contracts\Http\Request;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface RequestInterface
{
    public function __construct();

    public function __get(string $name);

    /**
     * @return ParameterBag
     */
    public function getPost();

    /**
     * @return ParameterBag
     */
    public function getGet();
    
    /**
     * @return ParameterBag
     */
    public function getCookies();

    /**
     * @return ParameterBag
     */
    public function getServer();

    /**
     * @return FileBag
     */
    public function getFiles();

    /**
     * @param string $method - Méthode passé en paramètre.
     * @return bool - True si request méthode est égal à la méthode passée en paramètre.
     */
    public function isMethod(string $method): bool;

    /**
     * @param array $methods - Méthodes passées en paramètre.
     * @return bool - True si request méthode est égal à une des méthodes passées en paramètre.
     */
    public function isInMethods(array $methods): bool;

    public function isGet(): bool;

    public function isPost(): bool;

    public function isPut(): bool;

    public function isDelete(): bool;

    public function isPatch(): bool;

    public function isHead(): bool;

    public function isOptions(): bool;

    public function isAjax(): bool;

    public function isCli(): bool;

    /**
     * @return string - Méthode utilisée pour accéder à la page : 'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD' ou 'OPTIONS'.
     */
    public function getMethod();

    /**
     * @return string - Méthode HTTP utilisée pour accéder à la page : 'GET' ou 'POST'.
     */
    public function getRequestMethod();

    /**
     * @return string - L'URL courante (sans les éventuels query params).
     */
    public function getUrlCurrent(): string;

    public function getFullUrlWithQuery(array $query);
}
