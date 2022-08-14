<?php

namespace DamianPhp\Support\String;

use DamianPhp\Date\Date;
use DamianPhp\Support\Facades\Str;
use DamianPhp\Contracts\String\SlugInterface;

/**
 * Pour créer des slugs (ajouts de posts...).
 * Peut fonctionner avec une Facade.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Slug implements SlugInterface
{
    private string $str;

    /**
     * Créer slug à parir d'une chaine de caractères.
     */
    public function create(string $str): string
    {
        $this->str = $str;

        foreach ($this->charactersArray() as $key => $value) {
            foreach ($value as $v) {
                if (Str::contains($this->str, $v)) {
                    $this->str = str_replace($v, $key, $this->str);
                }
            }
        }

        $this->cleanSlug();

        return $this->str;
    }

    /**
     * Si dans $this->str il y a carractère(s) qui n'existe(nt) pas dans les keys de $this->charactersArray() -> y remplacer par "-"
     */
    private function cleanSlug(): void
    {
        $strCharacters = str_split($this->str);
        
        foreach ($strCharacters as $character) {
            if (! array_key_exists($character, $this->charactersArray())) {
                $this->str = str_replace($character, '-', $this->str);
            }
        }

        $this->str = preg_replace('#-{1,1000}#', '-', $this->str);
        $this->str = trim($this->str, '-');

        if (mb_strlen($this->str) === 0) {
            $date = new Date();
            $this->str = $date->format('Y-m-d-H:i:s');
        }
    }

    /**
     * Créer keywords à parir d'une chaine de caractères.
     */
    public function createKeywords(string $str): string
    {
        $this->str = $str;

        foreach ($this->charactersArray() as $key => $value) {
            foreach ($value as $v) {
                if (Str::contains($this->str, $v)) {
                    if ($key === '-') {
                        $key = str_replace('-', ',', $key);
                    }
                    $this->str = str_replace($v, $key, $this->str);
                }
            }
        }

        $this->cleanKeywords();

        return $this->str;
    }

    /**
     * Si plusieurs tirets ou espaces ou virgules consécutifs -> 1 à la place.
     */
    private function cleanKeywords(): void
    {
        if (mb_strlen($this->str > 1)) {
            $this->str = preg_replace('# {1,1000}#', '-', $this->str);
            $this->str = preg_replace('#-{1,1000}#', '-', $this->str);
            $this->str = preg_replace('#,{1,1000}#', ',', $this->str);

            $this->str = trim($this->str, " \\\\&/~\#\(\"'\{\[|`_^@)\]}¨\$€¤£%\*!§/:;.,?+\-¤£%");

            $this->str = str_replace(',', ', ', $this->str);
        }
    }

    /**
     * Pour convertir les carractères spéciaux qui sont en Min, en carractère normal en Min
     *
     * @return array - Tablaux associatif avec des array en value
     */
    private function charactersArray(): array
    {
        return [
            'a' => [
                'à', 'á', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ',
                'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'ä', 'ā', 'ą',
                'å', 'α', 'ά', 'ἀ', 'ἁ', 'ἂ', 'ἃ', 'ἄ', 'ἅ', 'ἆ',
                'ἇ', 'ᾀ', 'ᾁ', 'ᾂ', 'ᾃ', 'ᾄ', 'ᾅ', 'ᾆ', 'ᾇ', 'ὰ',
                'ά', 'ᾰ', 'ᾱ', 'ᾲ', 'ᾳ', 'ᾴ', 'ᾶ', 'ᾷ', 'а', 'أ',
                'ǎ', 'ǻ',
                'Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ',
                'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'Ä', 'Å', 'Ā',
                'Ą', 'Α', 'Ά', 'Ἀ', 'Ἁ', 'Ἂ', 'Ἃ', 'Ἄ', 'Ἅ', 'Ἆ',
                'Ἇ', 'ᾈ', 'ᾉ', 'ᾊ', 'ᾋ', 'ᾌ', 'ᾍ', 'ᾎ', 'ᾏ', 'Ᾰ',
                'A', 'Ᾱ', 'Ὰ', 'Ά', 'ᾼ', 'А', 'Ǎ', 'Ǻ',
            ],

            'b' => [
                'б', 'β', 'Ъ', 'Ь', 'ب',
                'B', 'Б', 'Β',
            ],

            'c' => [
                'ç', 'ć', 'č', 'ĉ', 'ċ',
                'Ç','Ć', 'Č', 'Ĉ', 'Ċ',
                'C', '©',
            ],

            'd' => [
                'ď', 'ð', 'đ', 'ƌ', 'ȡ', 'ɖ', 'ɗ', 'ᵭ', 'ᶁ', 'ᶑ',
                'д', 'δ', 'د', 'ض',
                'D', 'Ď', 'Ð', 'Đ', 'Ɖ', 'Ɗ', 'Ƌ', 'ᴅ', 'ᴆ', 'Д', 'Δ',
            ],

            'e' => [
                'é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ',
                'ệ', 'ë', 'ē', 'ę', 'ě', 'ĕ', 'ė', 'ε', 'έ', 'ἐ',
                'ἑ', 'ἒ', 'ἓ', 'ἔ', 'ἕ', 'ὲ', 'έ', 'е', 'ё', 'э',
                'є', 'ə', '&', '€',
                'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ',
                'Ệ', 'Ë', 'Ē', 'Ę', 'Ě', 'Ĕ', 'Ė', 'Ε', 'Έ', 'Ἐ',
                'Ἑ', 'Ἒ', 'Ἓ', 'Ἔ', 'Ἕ', 'Έ', 'Ὲ', 'Е', 'Ё', 'Э',
                'E', 'Є', 'Ə',
            ],

            'f' => [
                'ф', 'φ', 'ف',
                'F', 'Ф', 'Φ',
            ],

            'g' => [
                'ĝ', 'ğ', 'ġ', 'ģ', 'г', 'ґ', 'γ', 'ج',
                'G', 'Ğ', 'Ġ', 'Ģ', 'Г', 'Ґ', 'Γ', 'ĝ',
            ],

            'h' => [
                'ĥ', 'ħ', 'η', 'ή', 'ح', 'ه',
                'H', 'Η', 'Ή', 'Ĥ', 'Ħ',
            ],

            'i' => [
                'í', 'ì', 'ỉ', 'ĩ', 'ị', 'î', 'ï', 'ī', 'ĭ', 'į',
                'ı', 'ι', 'ί', 'ϊ', 'ΐ', 'ἰ', 'ἱ', 'ἲ', 'ἳ', 'ἴ',
                'ἵ', 'ἶ', 'ἷ', 'ὶ', 'ί', 'ῐ', 'ῑ', 'ῒ', 'ΐ', 'ῖ',
                'ῗ', 'і', 'ї', 'и', 'ǐ',
                'I', 'Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'Î', 'Ï', 'Ī', 'Ĭ', 'Į',
                'İ', 'Ι', 'Ί', 'Ϊ', 'Ἰ', 'Ἱ', 'Ἳ', 'Ἴ', 'Ἵ', 'Ἶ',
                'Ἷ', 'Ῐ', 'Ῑ', 'Ὶ', 'Ί', 'И', 'І', 'Ї', 'Ǐ',
            ],

            'j' => [
                'ĵ', 'ј', 'Ј',
                'J', 'ĵ',
            ],

            'k' => [
                'ķ', 'ĸ', 'к', 'κ', 'Ķ', 'ق', 'ك',
                'K', 'К', 'Κ',
            ],

            'l' => [
                'ł', 'ľ', 'ĺ', 'ļ', 'ŀ', 'л', 'λ', 'ل',
                'L', 'Ĺ', 'Ł', 'Л', 'Λ', 'Ļ', 'Ľ', 'Ŀ',
            ],

            'm' => [
                'м', 'μ', 'م',
                'M', 'М', 'Μ',
            ],

            'n' => [
                'ñ', 'ń', 'ň', 'ņ', 'ŉ', 'ŋ', 'ν', 'н', 'ن',
                'N', 'Ń', 'Ñ', 'Ň', 'Ņ', 'Ŋ', 'Н', 'Ν',
            ],

            'o' => [
                'ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ',
                'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ø', 'ō', 'ő',
                'ŏ', 'ο', 'ὀ', 'ὁ', 'ὂ', 'ὃ', 'ὄ', 'ὅ', 'ὸ', 'ό',
                'ö', 'о', 'و', 'θ', 'ǒ', 'ǿ',
                'O', 'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ',
                'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'Ö', 'Ø', 'Ō',
                'Ő', 'Ŏ', 'Ο', 'Ό', 'Ὀ', 'Ὁ', 'Ὂ', 'Ὃ', 'Ὄ', 'Ὅ',
                'Ὸ', 'Ό', 'О', 'Θ', 'Ө', 'Ǒ', 'Ǿ',
            ],

            'p' => [
                'п', 'π',
                'P', 'П', 'Π',
            ],

            'q' => [
                'Q',
            ],

            'r' => [
                'ŕ', 'ř', 'ŗ', 'р', 'ρ', 'ر',
                'R', 'Ř', 'Ŕ', 'Р', 'Ρ', 'Ŗ',
            ],

            's' => [
                'ś', 'š', 'ş', 'с', 'σ', 'ș', 'ς', 'س', 'ص', '$', 'ŝ',
                'S', 'Ş', 'Ŝ', 'Ș', 'Š', 'Ś', 'С', 'Σ',
            ],

            't' => [
                'ť', 'ţ', 'т', 'τ', 'ț', 'ت', 'ط', 'ŧ',
                'Y', 'Ť', 'Ţ', 'Ŧ', 'Ț', 'Т', 'Τ', 'T',
            ],

            'u' => [
                'ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ',
                'ự', 'ü', 'û', 'ū', 'ů', 'ű', 'ŭ', 'ų', 'µ', 'у', 'ǔ',
                'ǖ', 'ǘ', 'ǚ', 'ǜ',
                'U', 'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ',
                'Ự', 'Û', 'Ü', 'Ū', 'Ů', 'Ű', 'Ŭ', 'Ų', 'У', 'Ǔ',
                'Ǖ', 'Ǘ', 'Ǚ', 'Ǜ',
            ],

            'v' => [
                'в',
                'V', 'В',
            ],

            'w' => [
                'ŵ', 'ω', 'ώ',
                'W', 'Ω', 'Ώ', 'Ŵ',
            ],

            'x' => [
                'χ',
                'X', 'Χ',
            ],

            'y' => [
                'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'ÿ', 'ŷ', 'й', 'ы', 'υ',
                'ϋ', 'ύ', 'ΰ', 'ي',
                'Y', 'Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Ÿ', 'Ῠ', 'Ῡ', 'Ὺ', 'Ύ',
                'Ы', 'Й', 'Υ', 'Ϋ', 'Ŷ',
            ],

            'z' => [
                'ź', 'ž', 'ż', 'з', 'ζ', 'ز',
                'Z', 'Ź', 'Ž', 'Ż', 'З', 'Ζ',
            ],

            'aa' => [
                'ع',
            ],

            'ae' => [
                'æ', 'ǽ',
                'Æ', 'Ǽ',
            ],

            'at' => [
                '@',
            ],

            'ch' => [
                'ч',
                'Ч',
            ],

            'dj' => [
                'ђ',
                'Ђ',
            ],

            'dz' => [
                'џ',
                'Џ',
            ],

            'gh' => [
                'غ',
            ],

            'ij' => [
                'Ĳ', 'ĳ',
            ],

            'kh' => [
                'х', 'خ',
                'Х',
            ],

            'lj' => [
                'љ',
                'Љ',
            ],

            'nj' => [
                'њ',
                'Њ',
            ],

            'oe' => [
                'œ',
                'Œ',
            ],

            'ps' => [
                'ψ',
                'Ψ',
            ],

            'sh' => [
                'ш',
                'Ш',
            ],

            'shch' => [
                'щ',
                'Щ',
            ],

            'ss' => [
                'ß',
                'ẞ',
            ],

            'th' => [
                'þ', 'ث', 'ذ', 'ظ',
                'Þ',
            ],

            'ts' => [
                'ц',
                'Ц',
            ],

            'ya' => [
                'я',
                'Я',
            ],

            'yu' => [
                'ю',
                'Ю',
            ],

            'zh' => [
                'ж',
                'Ж',
            ],

            '0' => [
                '°', '₀',
            ],

            '1' => [
                '¹', '₁',
            ],

            '2' => [
                '²', '₂',
            ],

            '3' => [
                '³', '₃',
            ],

            '4' => [
                '⁴', '₄',
            ],
            
            '5' => [
                '⁵', '₅',
            ],
            
            '6' => [
                '⁶',  '₆',
            ],
            
            '7' => [
                '⁷', '₇',
            ],
            
            '8' => [
                '⁸', '₈',
            ],
            
            '9' => [
                '⁹', '₉',
            ],

            '-' => [
                "\xC2\xA0", "\xE2\x80\x80", "\xE2\x80\x81",
                "\xE2\x80\x82", "\xE2\x80\x83", "\xE2\x80\x84",
                "\xE2\x80\x85", "\xE2\x80\x86", "\xE2\x80\x87",
                "\xE2\x80\x88", "\xE2\x80\x89", "\xE2\x80\x8A",
                "\xE2\x80\xAF", "\xE2\x81\x9F", "\xE3\x80\x80",
                ' ', '\\', '#', '\'', '(', '"', '{', '[', '~', '|',
                '`', '_', '^', ')', ']', '=', '}', '<',
                '>', '¨', '£', '¤', '*', '%', '!', '$', '/',
                ':', ';', '.', ',', '?', '+',
                'ſ', 'ƒ', '¿', '¡',
            ],
        ];
    }
}
