<?php

namespace DamianPhp\Support\String;

use DamianPhp\Support\Facades\Input;
use DamianPhp\Support\Facades\Request;
use DamianPhp\Support\Facades\Security;

/**
 * Gestion des String.
 * Peut fonctionner avec une Facade.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Str
{
    /**
     * Cache de snake-cased words.
     */
    private static $snakeCache = [];

    /**
     * Cache de snake-cased plurial words.
     */
    private static $snakePluralCache = [];
    
    /**
     * Cache de camel-cased words..
     */
    private static $camelCache = [];

    /**
     * Pour remplacer format camelCase par format snake_case.
     */
    public function convertCamelCaseToSnakeCase(string $value): string
    {
        if (isset(self::$snakeCache[$value])) {
            return self::$snakeCache[$value];
        }

        $withUpperArray = str_split($value);

        $snake_case = '';
        foreach ($withUpperArray as $letter) {
            if (preg_match("/[A-Z]/", $letter)) {
                $snake_case .= '_'.strtolower($letter);
            } else {
                $snake_case .= $letter;
            }
        }

        return self::$snakeCache[$value] = $snake_case;
    }
    
    /**
     * Pour remplacer format snake_case par format camelCase.
     */
    public function convertSnakeCaseToCamelCase(string $value): string
    {
        if (isset(self::$camelCache[$value])) {
            return self::$camelCache[$value];
        }

        return self::$camelCache[$value] = str_replace(' ', '', ucwords(str_replace('_', ' ', $value)));
    }

    /**
     * Obtenir en snake_case la forme plurielle d'un mot anglais.
     */
    public function snakePlural(string $value): string
    {
        if (isset(self::$snakePluralCache[$value])) {
            return self::$snakePluralCache[$value];
        }

        $snake_case = $this->convertCamelCaseToSnakeCase($value);
        $snakeCaseEx = explode('_', $snake_case);

        $plural = '';
        foreach ($snakeCaseEx as $word) {
            if ($word !== '') {
                if (mb_substr($word, -1, mb_strlen($word)) === 'y') { // si finit par "y" ...
                    $w = mb_substr($word, 0, -1);
                    $test = mb_substr($word, -2, mb_strlen($word));
                    if (preg_match("/^[BCDFGHJKLMNPQRSTVWXZ]+y$/i", $test)) {
                        $plural .= '_'.$w.'ies'; // ... si finit par "consonne + y" -> mettre "ies" à la place
                    } else {
                        $plural .= '_'.$word.'s'; // ... si finit par "voyelle + y" -> mettre "ys" à la place
                    }
                } elseif (mb_substr($word, -1, mb_strlen($word)) === 'o') {
                    $plural .= '_'.$word.'es'; // si finit par "o" -> mettre "oes" à la place
                } else {
                    $plural .= '_'.$word.'s'; // si non, ajouter un "s"
                }
            }
        }

        return self::$snakePluralCache[$value] = trim($plural, '_');
    }

    /**
     * Déterminer si une chaîne donnée contient une sous-chaîne donnée.
     *
     * @param string $haystack - Chaine dans laquelle faire la recherche.
     * @param string $needle - Ce que l'on recherche.
     */
    public function contains(string $haystack,  string $needle): bool
    {
        if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Chaine de caractères aléatoire
     */
    public function random(int $nbChars = 10, array $options = []): string
    {
        $randomString = '';

        $random_key = $options['random'] ?? 'abcdefghijklmnopqrstuvwxyz0123456789';
  
        for ($i = 0; $i < $nbChars; $i++) {
            $index = rand(0, strlen($random_key) - 1);
            $randomString .= $random_key[$index];
        }
      
        return $randomString;
    }

    /**
     * Pour menu actif.
     *
     * @param array $options
     * - $options['without_css_attr'] bool - Pour éventuellement ne pas retourner class="".
     * - $options[class'] bool - Pour éventuellement spécifier le nom de la classe CSS.
     */
    public function active(array|string $values, array $options = []): string
    {
        $class = $options['class'] ?? 'active';

        $values = (array) $values;

        foreach ($values as $value) {
            if (defined($value)) {
                if (isset($options['without_css_attr']) && $options['without_css_attr'] === true) {
                    return $class;
                }

                return 'class="'.$class.'"';
            }
        }

        return '';
    }

    /**
     * Pour sous menu actif.
     */
    public function active2(string $value): string
    {
        if (defined($value)) {
            return 'class="active2"';
        }

        return '';
    }

    /**
     * @param string $valueStr - Texte.
     * @param int $limit - Limite.
     * @return string - Extrait d'un texte sans couper un mot.
     */
    public function extract(string $valueStr, int $limit): string
    {
        if (self::contains($valueStr, '<img')) {
            $valueStr = preg_replace('/<img[^>]+>/i', '', $valueStr);
        }

        if (self::contains($valueStr, '<video')) {
            $valueStr = preg_replace('/<video[^>]+>/i', '', $valueStr);
        }

        if (self::contains($valueStr, '<iframe')) {
            $valueStr = preg_replace('/<iframe[^>]+>/i', '', $valueStr);
        }

        if (mb_strlen($valueStr) > $limit) {
            $str = mb_substr($valueStr, 0, $limit);
            $pSpace = strrpos($str, ' ');
            $text = mb_substr($str, 0, $pSpace);
            $points = '...';
        } else {
            $text = $valueStr;
            $points = '';
        }

        return $text.' '.$points;
    }

    /**
     * @param string $valueStr - Texte.
     * @param int $limit - Limite.
     * @return string Extrait d'un texte pour attr alt d'une img.
     */
    public function extractAlt(string $valueStr, int $limit = 30): string
    {
        $altImagePre = mb_substr($valueStr, 0, $limit);

        $result = self::contains($altImagePre, '.') ? mb_substr($altImagePre, 0, -mb_strlen(strrchr($altImagePre, '.'))) : $altImagePre;

        return mb_strlen($valueStr) > $limit ? $result.'...' : $result;
    }

    /**
     * @param array $items - Les URL et les titres.
     * @param null|array $options - Les éventuelles otpions (class css...).
     * @return string - Fil arianne.
     */
    public function getBreadcrumb(array $items, array $options = []): string
    {
        $olClassCss = (isset($options['class'])) ? ' '.$options['class'] : 'breadcrumb';

        $html = '<ol class="'.$olClassCss.'" itemscope itemtype="http://schema.org/BreadcrumbList">';

        $i = 1;
        foreach ($items as $url => $title) {
            $liCssActive = $i === count($items) ? ' class="active"' : '';

            $html .= '<li'.$liCssActive.' itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';

            if (is_string($url)) {
                // passera ici si on a bien spécifier l'URL en key
                $href = $url;
            } elseif ($i === count($items)) {
                // passera ici si lors du dernier item si on na pas spécifier l'URL en key
                $href = Request::getUrlCurrent();
            }

            $html .= '<a itemprop="item" href="'.$href.'">';
            $html .=     '<span itemprop="name">'.$title.'</span>';
            $html .= '</a>';
            $html .= '<meta itemprop="position" content="'.$i.'" />';
            
            if ($i < count($items)) {
                $html .= '<span class="chevron-right"></span>';
            }

            $html .= '</li>';

            $i++;
        }

        $html .= '</ol>';

        return $html; 
    }

    /**
     * Si plusieurs email séparés par virgules -> récupérer le 1er email.
     *
     * @param null|array $options
     * - $options['spam-filter'] string - Suported : 'without-signs', 'without-signs-encode', 'encode' - Pour anti-spam
     */
    public function firstEmail(string $email, array $options = []): string
    {
        $firstEmail = $this->firstElementWithoutComma(str_replace(' ', '', $email));

        if (isset($options['spam-filter'])) {
            if ($options['spam-filter'] === 'without-signs') {
                return $this->spamFilterForFirstEmail($firstEmail, '[AT]', '[POINT]');
            }

            if ($options['spam-filter'] === 'without-signs-encode') {
                return $this->spamFilterForFirstEmail($firstEmail, '%5BAT%5D', '%5BPOINT%5D');
            }

            elseif ($options['spam-filter'] === 'encode') {
                return $this->spamFilterForFirstEmail($firstEmail, '&commat;', '&period;');
            }
        }

        return $firstEmail;
    }

    private function spamFilterForFirstEmail(string $firstEmail, string $atEncode, string $pointEncode): string
    {
        $firstEmailWithouArobase = str_replace('@', $atEncode, $firstEmail);

        $extension = strrchr($firstEmailWithouArobase, '.');

        $firstEmailWithouArobaseWithoutExtension = mb_substr($firstEmailWithouArobase, 0, -mb_strlen($extension));

        $extensionWithoutPoint = str_replace('.', $pointEncode, $extension);

        return $firstEmailWithouArobaseWithoutExtension.$extensionWithoutPoint;
    }

    /**
     * Si plusieurs TEL séparés par virgules -> récupérer le 1er TEL.
     */
    public function firstTel(string $tel): string
    {
        return $this->firstElementWithoutComma($tel);
    }

    private function firstElementWithoutComma(string $value): string
    {
        $ex = explode(',', $value);

        return $ex[0];
    }

    /**
     * Convertir un TEL en format international.
     *
     * @param null|array $options
     * - options['space'] string - Pour ajouter des espaces.
     */
    public function telInternationalFormat(string $tel, array $options = []): string
    {
        if (mb_substr($tel, 0, 1) === '0') {
            if (isset($options['parentheses']) && $options['parentheses'] === true) {
                if (isset($options['space']) && $options['space'] === true) {
                    return '(+33) 0'.mb_substr($tel, 1);
                }

                return '(+33)0'.str_replace(' ', '', mb_substr($tel, 1));
            }

            if (isset($options['space']) && $options['space'] === true) {
                return '+33 '.mb_substr($tel, 1);
            }
            
            return '+33'.str_replace(' ', '', mb_substr($tel, 1));
        }

        if (isset($options['space']) && $options['space'] === true) {
            return $tel;
        }
        
        return str_replace(' ', '', $tel);
    }

    public function telFromDbToView(string $value): string
    {
        $letters = str_split($value);

        if (mb_substr($value, 0, 1) === '+') {
            $array = [4, 6, 8, 10];
        } else {
            $array = [2, 4, 6, 8];
        }

        $word = '';
        $i = 0;
        foreach ($letters as $letter) {
            $space = in_array($i, $array) ? ' ' : '';

            $word .= $space.$letter;

            $i++;
        }

        return $word;
    }

    /**
     * Pour pouvoir "cumuler" les <select> si il y en a déjà à $_GET
     * Pour par ex.: if(!empty($_GET['categorie'])) { echo '<input type="hidden" name="categorie" value="'.$_GET['categorie'].'">'; }
     *
     * @param array|string $gets - Valeur des éventuels GET.
     * @return string - Les éventuels GET.
     */
    public function inputHiddenIfHasQueryString(array|string $gets): string
    {
        $var = '';

        if (is_array($gets)) {
            foreach ($gets as $get) {
                if (Input::hasGet($get)) {
                    $var .= '<input type="hidden" name="'.$get.'" value="'.Input::get($get).'">';
                }
            }
        }  else {
            if (Input::hasGet($gets)) {
                $var .= '<input type="hidden" name="'.$gets.'" value="'.Input::get($gets).'">';
            }
        }

        return $var;
    }

    /**
     * Pour pouvoir "cumuler" les liens. Si de(s) GET passé(s) dans l'URL.
     * 
     * @param array|string $gets - Valeur des éventuelr GET.
     * @param $operatorParam|null - Pour éventuellement préciser l'opérateur.
     * @return string - Les éventuels GET.
     */
    public function andIfHasQueryString($gets, $operatorParam = null): string
    {
        $var = '';

        if (is_array($gets)) {
            $i = 0;
            foreach ($gets as $get) {
                if (Input::hasGet($get)) {
                    $operator = $i === 0 && $operatorParam !== null ? $operatorParam : '&amp;';
                    $var .= $operator.$get.'='. Input::get($get);
                    $i++;
                }
            }
        }  else {
            if (Input::hasGet($gets)) {
                $operator = $operatorParam ?? '&amp;';
                $var .= $operator.$gets.'='. Input::get($gets);
            }
        }

        return $var;
    }

    /**
     * Pour surligner mots clés entrés si search.
     * Fonctionne avec du CSS.
     *
     * @param string $titre
     * @param array|null $options
     * - $options['css_class'] string - Pour éventuellement préciser manuellemnt la class CSS.
     * - $options['input_search'] string - Pour éventuellement préciser un autre $_GET que 'search'.
     * - $options['nb_characters'] int - Pour éventuellement préciser à partir de combien de carractères entrés ajouter class css.
     */
    public function surligneIfSearch(string $titre, array $options = []): string
    {
        $classCss = $options['css_class'] ?? 'search-surligne';
        $search = $options['input_search'] ?? 'search';
        $nbCharacters = $options['nb_characters'] ?? 2;
        
        $title = Security::e($titre);

        if (Input::hasGet($search) && mb_strlen(Input::get($search)) > 0) {
            $words = explode(' ', Input::get($search));

            $i = 0;
            foreach ($words as $word) {
                if (mb_strlen($word) >= $nbCharacters) {
                    $i++;
                    if ($i > 4) $i = 1;
                    $title = str_ireplace($word, '<span class="'.$classCss.' '.$classCss.'-'.$i.'">'.$word.'</span>', $title);  
                }
            }
        }

        return $title;
    }
}
