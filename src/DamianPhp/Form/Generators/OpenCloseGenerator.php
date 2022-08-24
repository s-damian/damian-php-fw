<?php

namespace DamianPhp\Form\Generators;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Server;
use DamianPhp\Support\Facades\Request;

/**
 * Pour générer l'ouverture et la fermeture du form.
 *
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class OpenCloseGenerator
{
    private string $html;

    private const OPTIONS_KEYS_ALLOWED = ['id', 'class', 'style'];

    /**
     * Ouvrir un formulaire.
     *
     * @param array $options
     * - $options['action'] string - Pour éventuellement préciser l'URL de l'action.
     * - $options['method'] string - Pour éventuellement préciser la méthode HTTP (POST par defaut).
     * - $options['files'] string - Pour éventuellement si il y a un système d'upload dans le form.
     * - $options['on_submit'] string - Pour éventuellement ex. : "Etes vous sur de vouloir effectuer cette action... ?".
     * - $options['id'] string - Pour éventuellement ajouter un id au formulaire.
     * - $options['css'] string - Pour éventuellement ajouter une class CSS au formulaire.
     * - $options['style'] string - Pour éventuellement mettre du style CSS.
     */
    public function open(array $options = []): string
    {
        $action = isset($options['action']) ? $options['action'] : Server::getRequestUri();

        $method = (isset($options['method']) && (mb_strtoupper($options['method']) === 'POST' || mb_strtoupper($options['method']) === 'GET'))
            ? mb_strtoupper($options['method'])
            : 'POST';

        $enctypeUpload = (isset($options['files']) && $options['files'] === true)
            ? 'enctype="multipart/form-data"'
            : '';

        $onSubmit = (isset($options['on_submit']) && $options['on_submit'] !== null)
            ? 'onSubmit="return(confirm('.$options['on_submit'].'))"'
            : '';

        $this->html = '<form action="'.$action.'" method="'.$method.'" '.$enctypeUpload.' '.$onSubmit.'';

        $this->addOptions($options);

        $this->html .= '>';

        if (isset($options['method']) && in_array(mb_strtoupper($options['method']), Request::getMethodsAllowedForInputMethod())) {
            $this->html .= '<input name="_method" type="hidden" value="'.mb_strtoupper($options['method']).'">';
        }

        return $this->html;
    }

    /**
     * Eventuellement ajouter des options à la balise input.
     */
    private function addOptions(array $options): void
    {
        if ($options) {
            foreach ($options as $key => $value) {
                if ($key !== 'action' && $key !== 'method' && $key !== 'files' && $key !== 'on_submit') {
                    if (! in_array($key, self::OPTIONS_KEYS_ALLOWED)) {
                        Helper::getException('Key "'.$key.'" not authorized.');
                    } else {
                        $this->html .= ' '.$key.'="'.$value.'"';
                    }
                }
            }
        }
    }

    /**
     * Fermer un formulaire.
     */
    public function close(): string
    {
        return '</form>';
    }
}
