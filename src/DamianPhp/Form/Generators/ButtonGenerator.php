<?php

declare(strict_types=1);

namespace DamianPhp\Form\Generators;

use DamianPhp\Support\Helper;

/**
 * Pour générer un button.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class ButtonGenerator
{
    private const OPTIONS_KEYS_ALLOWED = ['name', 'class', 'id', 'style'];

    private string $html;

    private ?string $value;

    private array $options = [];

    /**
     * @param $value string|null - Texte à affiche dans le button.
     * @param array $options - Pour éventuellement ajouter au label id, class css.
     */
    public function __construct(?string $value = null, array $options = [])
    {
        $this->value = $value;
        $this->options = $options;
    }

    public function get(): string
    {
        $htmlValue = $this->value ?? lang('form')['button'];

        $this->html = '<button type="button"';

        $this->addOptions();

        $this->html .= '>'.$htmlValue.'</button>';

        return $this->html;
    }

    /**
     * Eventuellement ajouter des options à la balise input.
     */
    private function addOptions(): void
    {
        if (count($this->options) > 0) {
            foreach ($this->options as $k => $v) {
                if (! in_array($k, self::OPTIONS_KEYS_ALLOWED)) {
                    Helper::getException('Key "'.$k.'" not authorized.');
                } else {
                    $this->html .= ' '.$k.'="'.$v.'"';
                }
            }
        }
    }
}
