<?php

namespace DamianPhp\Routing;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Server;
use DamianPhp\Contracts\Routing\RouterInterface;

/**
 * Pour l'éventuelle internationalisation.
 * Gestion des langues du Routing. Communique avec la classe Router.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class LangParsing
{
    /**
     * L'instance router.
     */
    private RouterInterface $router;

    /**
     * Pour éventuellement activer l'internationalisation.
     */
    private bool $trans = false;

    /**
     * Pour si l'internationalisation est éctivé et qu'on veut modifier la langue par default.
     */
    private string $defaultLang;

    /**
     * ResourceRegistrarconstructor.
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Pour préciser dans la liste des routes les URL où on veut faire de l'internolisation.
     */
    public function trans(bool $bool): void
    {
        $this->trans = Helper::isMultilingual() ? $bool : false;
    }

    /**
     * - Vérifier que l'internationalisation est activé,
     *  -et que 'config/lang/address_structure' est 'subdirectories' -> ajouter lang au préfix du path de la route en répertoire.
     * - Si langue est dans 'languages_allowed'.
     *
     * @return string - La lang a ajouter en préfix du path de la route.
     */
    public function getLangForAddPathRoute(): string
    {
        if ($this->trans === true && Helper::config('lang')['address_structure'] === 'subdirectories') {
            if (in_array($this->getLang(), Helper::config('lang')['languages_allowed'])) {
                return $this->getLang().'/';
            }
        }
        
        return '';
    }

    /**
     * - Si lang 'default' n'est pas dans 'languages_allowed' -> erreur.
     * - Vérifier que l'internationalisation est activé, et que l'URL GET "testé" est bien dans 'languages_allowed'.
     *
     * @return string - L'éventuelle langue (celle de l'URL sous la forme 'fr', ou celle par defaut).
     */
    public function getLang(): string
    {
        static $lang;

        if ($lang === null) {
            if (! in_array(Helper::config('lang')['default'], Helper::config('lang')['languages_allowed'])) {
                Helper::getException('lang "default" must be in "languages_allowed".');
            }

            $langUrl = $this->parseLangWithUrl();

            if (Helper::isMultilingual() && in_array($langUrl, Helper::config('lang')['languages_allowed'])) {
                $lang = $langUrl;
            } else {
                $lang = $this->getDefaultLang();
            }
        }

        return $lang;
    }

    /**
     * Retourne lang selon structure d'addresse choisie pour faire fonctionner l'internationalisation.
     *
     * - 'domain' - Fonctionne en version d'addresse en domaine.
     *   Exemple : France : domaine-name.fr - Espagne : domaine-name.es
     *
     * - 'subdomain' - Fonctionne en version d'addresse en sous-domaine.
     *   Exemple : France : fr.domaine-name.com - Espagne : es.domaine-name.com
     *
     * - 'subdirectories' - Fonctionne en version d'addresse en répertoire.
     *   Exemple : France : domaine-name.com/fr/ - Espagne : domaine-name.com/es/
     *
     * - domain_and_subdomain - Fonctionne en version d'addresse en domaine et sous-domaine pour certaines versions (qu'il faut préciser dans 'subdomain_languages').
     *   Exemple : France : domaine-name.fr - USA : domaine-name.com - Espagne : es.domaine-name.com
     *
     * @return string
     */
    private function parseLangWithUrl(): string
    {
        if (! isset(Helper::config('lang')['address_structure'])) {
            return '';
        }
        
        switch (Helper::config('lang')['address_structure']) {
            case 'domain':
                return $this->parseWithDomain();
            case 'subdomain':
                return $this->parseWithSubdomain();
            case 'subdirectories':
                return $this->parseWithSubdirectories();
            case 'domain_and_subdomain':
                return $this->parseWithDomainAndSubdomain();
            default:
                return '';
        }
    }

    private function parseWithDomain(): string
    {
        $extension = strrchr(Server::getHttpHost(), '.');

        if (array_key_exists($extension, Helper::config('lang')['extension_languages'])) {
            $ext = Helper::config('lang')['extension_languages'][$extension];
        } else {
            $ext = $extension;
        }

        return str_replace('.', '', $ext);
    }

    private function parseWithSubdomain(): string
    {
        $array = explode('.', Server::getHttpHost());

        return array_values($array)[0] !== 'www' ? array_values($array)[0] : array_values($array)[1];
    }

    private function parseWithSubdirectories(): string
    {
        $urlEx = explode('/', $this->router->getUri());

        return $urlEx[0];
    }

    private function parseWithDomainAndSubdomain(): string
    {
        $explode = explode('.', Server::getHttpHost());

        $subdomain = ($explode[0] !== 'www') ? $explode[0].'.' : $explode[1].'.';

        if (array_key_exists($subdomain, Helper::config('lang')['subdomain_languages'])) {
            $ext = Helper::config('lang')['subdomain_languages'][$subdomain];
        } else {
            if (array_key_exists('.'.end($explode), Helper::config('lang')['extension_languages'])) {
                $ext = Helper::config('lang')['extension_languages']['.'.end($explode)];
            } else {
                $ext = '.'.end($explode);
            }
        }

        return str_replace('.', '', $ext);
    }

    /**
     * @return string - Langue par default.
     */
    private function getDefaultLang(): string
    {
        if (Helper::isMultilingual() && in_array($this->defaultLang, Helper::config('lang')['languages_allowed'])) {
            return $this->defaultLang;
        } else {
            return Helper::config('lang')['default'];
        }
    }

    /**
     * Eventuellement modifier langue par défaut (avec session, cookie, ou géolocalisation par exemple).
     * Que lorsque lang n'est pas précisé dans URL que ça redirige vers nouvelle lang par defaut.
     */
    public function setDefaultLang(string $lang): void
    {
        $this->defaultLang = $lang;
    }
}
