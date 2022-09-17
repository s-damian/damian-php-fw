<?php

namespace DamianPhp\Form\Generators;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Input;
use DamianPhp\Support\Facades\Request;

/**
 * Pour générer un select.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class SelectGenerator
{
    private string $html;

    private string $name;

    private array $balisesOption = [];

    private null|int|string $selectedPerDefault;

    private array $options = [];

    /**
     * Pour les options que l'on "push" dans le html de la balise select.
     */
    private const OPTIONS_KEYS_ALLOWED = ['id', 'class', 'style'];

    /**
     * @param string $name - Name du <select>
     * @param array $balisesOption - Les <option>
     * @param string|int|null $selectedPerDefault - Pour éventuellement ajouter un selected active par default.
     * @param array $options - Pour éventuellement ajouter au select : id, class, style, autosubmit.
     */
    public function __construct(string $name, array $balisesOption, $selectedPerDefault = null, array $options = [])
    {
        $this->name = $name;
        $this->balisesOption = $balisesOption;
        $this->selectedPerDefault = $selectedPerDefault;
        $this->options = $options;
    }

    public function get(): string
    {
        $autoSubmit = isset($this->options['autosubmit'])
            ? 'onchange="document.getElementById(\''.$this->options['autosubmit'].'\').submit();"'
            : '';

        $this->html = '<select '.$autoSubmit.' name="'.$this->name.'"';

        $this->addOptions();

        $this->html .= !isset($this->options['id']) ? ' id="'.$this->name.'"' : '';

        $this->html .= '>';

        $this->generateBalisesOption();

        $this->html .= '</select>';

        return $this->html;
    }

    /**
     * Eventuellement ajouter des options à la balise input.
     */
    private function addOptions(): void
    {
        if (count($this->options) > 0) {
            foreach ($this->options as $key => $value) {
                if (! in_array($key, ['autosubmit', 'options_disabled'])) { // certaines options, on ne les "push" pas direct dans le html de la balise select
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
     * Générer les balises <option>
     */
    private function generateBalisesOption(): void
    {
        foreach ($this->balisesOption as $keyVal => $textVal) {
            // si il y a des <optgroup>
            if (is_array($textVal)) {
                $this->html .= '<optgroup label="'.$keyVal.'">';
                foreach ($textVal as $kVal => $vVal) {
                    $selected = $this->selectedPerDefault !== null && $this->selectedPerDefault === $kVal ? 'selected' : '';
                    $this->html .= '<option '.$selected.' value="'.$kVal.'">'.$vVal.'</option>';
                }
                $this->html .= '</optgroup>';
            }
            // si il n'y a pas de <optgroup>
            else {
                if (Request::isPost()) {
                    $selected = Input::post($this->name) === $keyVal ? 'selected' : '';
                } else {
                    $selected = $this->selectedPerDefault !== null && $this->selectedPerDefault === $keyVal ? 'selected' : '';
                }

                $disabled = isset($this->options['options_disabled']) && in_array($keyVal, $this->options['options_disabled']) ? ' disabled' : '';

                $this->html .= '<option '.$selected.' value="'.$keyVal.'"'.$disabled.'>'.$textVal.'</option>';
            }
        }
    }
}
