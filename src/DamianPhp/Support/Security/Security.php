<?php

namespace DamianPhp\Support\Security;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Str as StrF;

/**
 * Sécurité - Faille XSS, faille CSRF, cryptage, extension upload.
 * Peut fonctionner avec une Facade.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Security
{
    /**
     * Contre faille XSS - Pour sécuriser les echo.
     */
    public function e(mixed $value): string
    {
        return $value !== null ? htmlspecialchars($value) : '';
    }

    /**
     * Contre faille XSS (surtout pour ckeditor).
     */
    public function purify(mixed $value): string
    {
        return $value !== null ? preg_replace('#<script(.*?)>(.*?)</script>#is', '', $value) : '';
    }

    /**
     * Contre faille CRLF.
     * Interdire les retours chariot dans un champ (surtout pour "Mot de passe oublié ?").
     */
    public function noCrlf(string $value): string
    {
        return str_replace(['\n', '\r', PHP_EOL, '%0A', '<br>', '</br>'], '', $value);
    }

    public function hash(string $value): string
    {
        return sha1($value);
    }

    /*
    |--------------------------------------------------------------------------
    | Extensions:
    |--------------------------------------------------------------------------
    */

    /**
     * Pour connaitre l'extension du param $file.
     */
    public function getExtFile(string $file): string
    {
        $s = strrchr($file, '.');

        return ltrim($s, '.');
    }

    /**
     * Pour vérifier que l'extention est bien un audio.
     */
    public static function extsAudioValid(): array
    {
        return Helper::config('security')['exts_audio_valid'];
    }

    /**
     * Pour vérifier que l'extention est bien un document.
     */
    public function extsDocValid(): array
    {
        return Helper::config('security')['exts_doc_valid'];
    }

    /**
     * Pour vérifier que l'extention est bien une image.
     */
    public function extsImgValid(): array
    {
        return Helper::config('security')['exts_img_valid'];
    }

    /**
     * Pour vérifier que l'extention est bien une video.
     */
    public static function extsVideoValid(): array
    {
        return Helper::config('security')['exts_video_valid'];
    }

    /**
     * Pour sécuriser les pièces jointes.
     */
    public function extsFileValid(): array
    {
        $a = array_merge($this->extsImgValid(), $this->extsAudioValid());

        $b = array_merge($a, $this->extsVideoValid());

        $c = array_merge($b, $this->extsDocValid());

        return array_merge($c, Helper::config('security')['exts_other_valid']);
    }
}
