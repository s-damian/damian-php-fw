<?php

declare(strict_types=1);

namespace DamianPhp\Support\String;

use DamianPhp\Support\Helper;

/**
 * Gestion des medias (vidéos, audio...).
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Media
{
    /**
     * Retourne un nom de fichier sans extention.
     */
    public function getFileNameWithoutExt(string $file): string
    {
        return mb_substr($file, 0, strrpos($file, '.'));
    }

    /**
     * Retourne nom de fichier d'une image (on tente toutes les extentions).
     */
    public function getFileNameImgWithExt(string $image): string
    {
        foreach (Helper::config('security')['exts_img_valid'] as $extension) {
            $pathImgArticle = Helper::publicPath($image.$extension);

            if (file_exists($pathImgArticle)) {
                return $image.$extension;
            }
        }

        return '';
    }

    /**
     * Retourne src 'dune image (on tente toutes les extentions).
     */
    public function getSrcImgWithExt(string $image): string
    {
        return $this->getFileNameImgWithExt($image) !== '' ? Helper::getBaseUrl().'/'.$this->getFileNameImgWithExt($image) : '';
    }

    public static function geLinkPreload(string $file, array $options = []): string
    {
        $as = isset($options['as']) ? ' as="'.$options['as'].'"' : '';
        $type = isset($options['type']) ? ' type="'.$options['type'].'"' : '';

        if (isset($options['crossorigin'])) {
            if ($options['crossorigin'] === 'crossorigin') {
                $crossorigin = ' crossorigin';
            } else {
                $crossorigin = ' crossorigin="'.$options['crossorigin'];
            }
        } else {
            $crossorigin = '';
        }

        return '<link rel="preload" href="'.Helper::getBaseUrl().'/'.$file.'"'.$as.$type.$crossorigin.'>';
    }

    public function getCssWithV(string $file): string
    {
        return '<link rel="stylesheet" type="text/css" href="'.Helper::getBaseUrl().'/'.$file.'?v='.filemtime(Helper::publicPath($file)).'">';
    }

    public function getJsWithV(string $file): string
    {
        return '<script src="'.Helper::getBaseUrl().'/'.$file.'?v='.filemtime(Helper::publicPath($file)).'"></script>';
    }

    public function getImgWithV(string $file, string $alt = 'Image', array $options = []): string
    {
        $html = '';
        foreach ($options as $k => $v) {
            $html .= ' '.$k.'="'.$v.'"';
        }

        return '<img src="'.Helper::getBaseUrl().'/'.$file.'?v='.filemtime(Helper::publicPath($file)).'" alt="'.$alt.'"'.$html.'>';
    }

    /**
     * @param string $audio - Path de l'audio sans extension.
     * @param array $types - Extensions voulues.
     * @param array $options - Pour éventuellement ajouter : id, class.
     * @return string - Html audio.
     */
    public function getAudio(string $audio, array $types, array $options = []): string
    {
        $html = '<audio controls';

        if ($options) {
            $array_key = ['id', 'class'];
            foreach ($options as $key => $value) {
                if (! in_array($key, $array_key) && ! $options['poster']) {
                    Helper::getException('Key "'.$key.'" not authorized in options of audio');
                } else {
                    $html .= ' '.$key.'="'.$value.'"';
                }
            }
        }

        $html .= '>';

        $array_types = ['mp3', 'ogg', 'mpeg', 'wav', 'wawe', 'aif', 'aac', 'm4a', 'wma'];
        foreach ($types as $type) {
            if (! in_array($type, $array_types)) {
                Helper::getException('Type "'.$type.'" of audio not authorized');
            } else {
                $html .= '<source src="'.$audio.'.'.$type.'" type="audio/'.$type.'">';
            }
        }

        $html .= '</audio>';

        return $html;
    }

    /**
     * @param string $video - Path de la vidéo sans extension.
     * @param array $types - Extensions voulues.
     * @param array $options - Pour éventuellement ajouter : height, width, id, class, poster.
     * @return string - Html vidéo.
     */
    public function getVideo(string $video, array $types, array $options = []): string
    {
        $poster = (isset($options['poster'])) ? 'poster="'.$options['poster'].'"' : ''; // éventuellement ajouter une img de fond par defaut (si vidéo à l'arret)
        $html = '<video controls '.$poster.'';

        if ($options) {
            $array_key = ['height', 'width', 'id', 'class'];
            foreach ($options as $key => $value) {
                if (! in_array($key, $array_key) && ! $options['poster']) {
                    Helper::getException('Key "'.$key.'" not authorized in options of video');
                } else {
                    $html .= ' '.$key.'="'.$value.'"';
                }
            }
        }

        $html .= '>';

        $array_types = ['ogv', 'mp4', 'webm', 'ogg'];
        foreach ($types as $type) {
            // mp4 : IE, Safari / ogv : Firefox, Chrome, Opera / webm : Firefox, Chrome
            if (! in_array($type, $array_types)) {
                Helper::getException('Type "'.$type.'" of video not authorized');
            } else {
                $html .= '<source src="'.$video.'.'.$type.'" type="video/'.$type.'">';
            }
        }

        $html .= '</video>';

        return $html;
    }
}
