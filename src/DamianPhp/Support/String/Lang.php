<?php

declare(strict_types=1);

namespace DamianPhp\Support\String;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Server;
use DamianPhp\Support\Facades\Str as StrF;

/**
 * Pour retourner des string avec le language (balises avec attr hreflang, img pour languages).
 * Peut fonctionner avec une Facade.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Lang
{
    /**
     * Pour éventuellent ajouter des path à images de languages.
     * Pour rediriger vers pas en cours au lieu de l'accueil.
     */
    private static array $hrefsImglang = [];

    /**
     * Ajouter des path à hreflang.
     */
    private static array $pathsHreflang = [];

    public static function hasCountryLanguage(string $lang): bool
    {
        return isset(Helper::config('lang')['countries_languages']) && array_key_exists($lang, Helper::config('lang')['countries_languages']);
    }

    public static function getCountryLanguage(string $lang): string
    {
        if (self::hasCountryLanguage($lang)) {
            return Helper::config('lang')['countries_languages'][$lang];
        }

        return '';
    }

    /**
     * Pour si des hreflang ne sont pas prévus pour une "resource", et qu'on veut ajouter quelques hreflang.
     * (ex. : pour des posts étant données que les URL ne sont pas le même selons les lang).
     * Pour éventuellemnt faire ceci par exemple :
     *
     * echo Lang::addHreflang([
     *     'fr' => 'articles/laravel-framework-php'
     *     'en' => 'articles/laravel-php-framework',
     *     'es' => 'articles/laravel-php-es-framework',
     * ]);
     *
     * @param array $linksToParse - Les URL à "traiter". En keys les valeurs de lang, en values les URL à mettre à la place de l'URL de la lang locale.
     */
    public static function addSpecificHreflang(array $linksToParse): void
    {
        if (StrF::contains(Helper::getActiveUrl(), $linksToParse[Helper::getLocale()])) {
            $i = 0;

            foreach (Helper::config('lang')['languages_allowed'] as $value) {
                $href = '';
                $test = self::getHrefUnderAddressStructure($value);

                if ($value === Helper::getLocale()) {
                    $href .= $test;
                } else {
                    if (isset($linksToParse[$value])) {
                        $href .= str_replace($linksToParse[Helper::getLocale()], $linksToParse[$value], $test);
                    }
                }

                if ($href !== '') {
                    self::$pathsHreflang[$i]['lang'] = $value;
                    self::$pathsHreflang[$i]['href'] = $href;

                    self::$hrefsImglang[$value] = $href;
                }

                $i++;
            }
        }
    }

    /**
     * Ajouter une balise hreflang (prend URL ateuelle, et remplace locale par les autres lang allowed).
     */
    public static function addHreflang(): void
    {
        if (self::$pathsHreflang === []) {
            $i = 0;

            foreach (Helper::config('lang')['languages_allowed'] as $value) {
                $href = self::getHrefUnderAddressStructure($value);

                self::$pathsHreflang[$i]['lang'] = $value;
                self::$pathsHreflang[$i]['href'] = $href;

                self::$hrefsImglang[$value] = $href;

                $i++;
            }
        }
    }

    private static function getHrefUnderAddressStructure(string $value): string
    {
        $href = '';

        if (Helper::config('lang')['address_structure'] === 'domain') {
            $href .= self::getHrefIfAddressStructureIsDomain($value);
        } elseif (Helper::config('lang')['address_structure'] === 'subdomain') {
            $href .= self::getHrefIfAddressStructureIsSubdomain($value);
        } elseif (Helper::config('lang')['address_structure'] === 'subdirectories') {
            $href .= self::getHrefIfAddressStructureIsSubdirectories($value);
        } elseif (Helper::config('lang')['address_structure'] === 'domain_and_subdomain') {
            $href .= self::getHrefIfAddressStructureIsDomainAndSubdomain($value);
        }

        return $href;
    }

    /**
     * @return string - Les balises link avec leur attr hreflang.
     */
    public static function getHreflang(): string
    {
        $stringStart = '';
        $string = '';

        if (count(self::$pathsHreflang) > 1) {
            foreach (self::$pathsHreflang as $value) {
                $country = self::hasCountryLanguage($value['lang'])
                    ? '-'.self::getCountryLanguage($value['lang'])
                    : '';

                if ($value['lang'] === Helper::getLocale()) {
                    $stringStart .= '<link rel="alternate" href="'.$value['href'].'" hreflang="'.$value['lang'].$country.'" />';
                } else {
                    $string .= '<link rel="alternate" href="'.$value['href'].'" hreflang="'.$value['lang'].$country.'" />';
                }
            }
        }

        return $stringStart.$string;
    }

    /**
     * @return string - Les balises img des languages.
     */
    public static function getImglang(): string
    {
        $string = '';

        if (! isset(Helper::config('lang')['address_structure'])) { // Sécurité.
            return '';
        }

        if (Helper::config('lang')['address_structure'] === 'domain') {
            foreach (Helper::config('lang')['languages_allowed'] as $value) {
                if (isset(self::$hrefsImglang[$value])) {
                    $href = self::$hrefsImglang[$value];
                } else {
                    $href = self::getHrefIfAddressStructureIsDomain($value, ['root'=>true]);
                }

                $string .= '<a data-lang="'.$value.'" href="'.$href.'">';
                $string .=     '<img src="'.Helper::getBaseUrl().'/medias/images/flags/'.$value.'.png"';
                $string .=     ' alt="'.array_search($value, Helper::config('lang')['languages_allowed']).'">';
                $string .= '</a>';
            }
        } elseif (Helper::config('lang')['address_structure'] === 'subdomain') {
            foreach (Helper::config('lang')['languages_allowed'] as $value) {
                if (isset(self::$hrefsImglang[$value])) {
                    $href = self::$hrefsImglang[$value];
                } else {
                    $href = self::getHrefIfAddressStructureIsSubdomain($value, ['root'=>true]);
                }

                $string .= '<a data-lang="'.$value.'" href="'.$href.'">';
                $string .=     '<img src="'.Helper::getBaseUrl().'/medias/images/flags/'.$value.'.png"';
                $string .=     ' alt="'.array_search($value, Helper::config('lang')['languages_allowed']).'">';
                $string .= '</a>';
            }
        } elseif (Helper::config('lang')['address_structure'] === 'subdirectories') {
            foreach (Helper::config('lang')['languages_allowed'] as $value) {
                if (isset(self::$hrefsImglang[$value])) {
                    $href = self::$hrefsImglang[$value];
                } else {
                    $href = self::getHrefIfAddressStructureIsSubdirectories($value, ['root'=>true]);
                }

                $string .= '<a data-lang="'.$value.'" href="'.$href.'">';
                $string .=     '<img src="'.Helper::getBaseUrl().'/medias/images/flags/'.$value.'.png"';
                $string .=     ' alt="'.array_search($value, Helper::config('lang')['languages_allowed']).'">';
                $string .= '</a>';
            }
        } elseif (Helper::config('lang')['address_structure'] === 'domain_and_subdomain') {
            foreach (Helper::config('lang')['languages_allowed'] as $value) {
                if (isset(self::$hrefsImglang[$value])) {
                    $href = self::$hrefsImglang[$value];
                } else {
                    $href = self::getHrefIfAddressStructureIsDomainAndSubdomain($value, ['root'=>true]);
                }

                $string .= '<a data-lang="'.$value.'" href="'.$href.'">';
                $string .=     '<img src="'.Helper::getBaseUrl().'/medias/images/flags/'.$value.'.png"';
                $string .=     ' alt="'.array_search($value, Helper::config('lang')['languages_allowed']).'">';
                $string .= '</a>';
            }
        }

        return $string;
    }

    /*
    |--------------------------------------------------------------------------
    | Returner liens pour hrelang et images flags :
    |--------------------------------------------------------------------------
    */

    private static function getHrefIfAddressStructureIsDomain(string $value, array $options = []): string
    {
        $langWhereArraySearch = (array_search($value, Helper::config('lang')['extension_languages']) !== false)
            ? array_search($value, Helper::config('lang')['extension_languages'])
            : $value;
        $localeFromUrl = (array_search(Helper::getLocale(), Helper::config('lang')['extension_languages']) !== false)
            ? array_search(Helper::getLocale(), Helper::config('lang')['extension_languages'])
            : Helper::getLocale();
        $serverWithoutLocaleFromUrl = str_replace($localeFromUrl, '', Server::getHttpHost());
        $serverWithLang = $serverWithoutLocaleFromUrl.'.'.str_replace('.', '', $langWhereArraySearch);

        $inReplace = isset($options['root']) ? Helper::getBaseUrl() : Helper::getActiveUrl();

        return str_replace(Server::getHttpHost(), str_replace('..', '.', $serverWithLang), $inReplace);
    }

    private static function getHrefIfAddressStructureIsSubdomain(string $value, array $options = []): string
    {
        if (StrF::contains(Server::getHttpHost(), 'www.')) {
            $serverWithoutLocaleFromUrlAndWithoutWww = str_replace([Helper::getLocale().'.', 'www.'], '', Server::getHttpHost());
            $serverWithLang = 'www.'.str_replace('.', '', $value).'.'.$serverWithoutLocaleFromUrlAndWithoutWww;
        } else {
            $serverWithoutLocaleFromUrl = str_replace(Helper::getLocale().'.', '', Server::getHttpHost());
            $serverWithLang = str_replace('.', '', $value).'.'.$serverWithoutLocaleFromUrl;
        }

        $inReplace = isset($options['root']) ? Helper::getBaseUrl() : Helper::getActiveUrl();

        return str_replace(Server::getHttpHost(), $serverWithLang, $inReplace);
    }

    private static function getHrefIfAddressStructureIsSubdirectories(string $value, array $options = []): string
    {
        $inReplace = isset($options['root']) ? Helper::getBaseUrl() : Helper::getActiveUrl();

        return str_replace('/'.Helper::getLocale().'/', '/'.str_replace('/', '', $value).'/', $inReplace);
    }

    private static function getHrefIfAddressStructureIsDomainAndSubdomain(string $value, array $options = []): string
    {
        $inReplace = isset($options['root']) ? Helper::getBaseUrl() : Helper::getActiveUrl();

        if (array_key_exists(Helper::getLocale().'.', Helper::config('lang')['subdomain_languages'])) {
            $langWhereArraySearch = (array_search($value, Helper::config('lang')['extension_languages']) !== false)
                ? array_search($value, Helper::config('lang')['extension_languages'])
                : $value;

            $localeFromUrl = (array_search(Helper::getLocale(), Helper::config('lang')['extension_languages']) !== false)
                ? array_search(Helper::getLocale(), Helper::config('lang')['extension_languages'])
                : Helper::getLocale();

            $serverWithoutLocaleFromUrl = str_replace($localeFromUrl, '', Server::getHttpHost());

            if (array_key_exists($value.'.', Helper::config('lang')['subdomain_languages'])) {
                if (StrF::contains(Server::getHttpHost(), 'www.')) {
                    $serverWithoutWww = str_replace('www.', '', $serverWithoutLocaleFromUrl);
                    $serverWithLang = 'www.'.Helper::config('lang')['subdomain_languages'][$value.'.'].'.'.$serverWithoutWww;
                } else {
                    $serverWithLang = Helper::config('lang')['subdomain_languages'][$value.'.'].'.'.$serverWithoutLocaleFromUrl;
                }
            } else {
                $serverWithoutExtFromUrl = str_replace(Helper::config('lang')['extension_international'], '', $serverWithoutLocaleFromUrl);

                $serverWithLang = ltrim($serverWithoutExtFromUrl, '.').'.'.str_replace('.', '', $langWhereArraySearch);
            }

            $href = str_replace(Server::getHttpHost(), str_replace('..', '.', $serverWithLang), $inReplace);
        } else {
            $langWhereArraySearch = (array_search($value, Helper::config('lang')['extension_languages']) !== false)
                ? array_search($value, Helper::config('lang')['extension_languages'])
                : $value;

            $localeFromUrl = (array_search(Helper::getLocale(), Helper::config('lang')['extension_languages']) !== false)
                ? array_search(Helper::getLocale(), Helper::config('lang')['extension_languages'])
                : Helper::getLocale();

            $serverWithoutLocaleFromUrl = str_replace($localeFromUrl, '', Server::getHttpHost());

            if (array_key_exists($value.'.', Helper::config('lang')['subdomain_languages'])) {
                if (StrF::contains(Server::getHttpHost(), 'www.')) {
                    $serverWithoutWww = str_replace('www.', '', $serverWithoutLocaleFromUrl);
                    $serverWithLang = 'www.'.Helper::config('lang')['subdomain_languages'][$value.'.'].'.'.$serverWithoutWww.Helper::config('lang')['extension_international'];
                } else {
                    $serverWithLang = Helper::config('lang')['subdomain_languages'][$value.'.'].'.'.$serverWithoutLocaleFromUrl.Helper::config('lang')['extension_international'];
                }
            } else {
                $serverWithLang = $serverWithoutLocaleFromUrl.'.'.str_replace('.', '', $langWhereArraySearch);
            }

            $href = str_replace(Server::getHttpHost(), str_replace('..', '.', $serverWithLang), $inReplace);
        }

        return $href;
    }

    /*
    |--------------------------------------------------------------------------
    | La balise img de la langue locale :
    |--------------------------------------------------------------------------
    */

    /**
     * @return string - La balise img de la langue locale
     */
    public static function getImglangActive(): string
    {
        $toReturn = [];

        if (! isset(Helper::config('lang')['address_structure'])) { // Sécurité.
            return '';
        }

        foreach (Helper::config('lang')['languages_allowed'] as $value) {
            if (Helper::config('lang')['address_structure'] === 'domain' && array_key_exists($value, Helper::config('lang')['extension_languages'])) {
                $langWhereArraySearch = Helper::config('lang')['extension_languages'][$value];
            } else {
                $langWhereArraySearch =  $value;
            }

            if ($langWhereArraySearch === Helper::getLocale()) {
                $toReturn = [
                    'src' => Helper::getLocale(),
                    'alt' => 'Language active : '.array_search($value, Helper::config('lang')['languages_allowed']),
                ];

                break;
            }
        }

        $html =  '<a class="language-active" href="'.Helper::getActiveUrl().'">';
        $html .=     '<img src="'.Helper::getBaseUrl().'/medias/images/flags/'.$toReturn['src'].'.png" alt="'.$toReturn['alt'].'">';
        $html .= '<div class="arrow"></div></a>';

        return $html;
    }
}
