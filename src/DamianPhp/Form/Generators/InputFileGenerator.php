<?php

namespace DamianPhp\Form\Generators;

use DamianPhp\Support\Helper;

/**
 * Pour générer un input de type file.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class InputFileGenerator
{
    private string $html;

    private string $name;

    private array $options = [];

    private const OPTIONS_KEYS_ALLOWED = ['id', 'class', 'style'];

    /**
     * @param string $name - Name de l'input.
     * @param array $options - Pour éventuellement ajouter au label id, class css.
     */
    public function __construct(string $name, array $options = [])
    {       
        $this->name = $name;
        $this->options = $options;
    }

    public function get(): string
    {
        $multiple = isset($this->options['multiple']) && $this->options['multiple'] === true ? 'multiple="multiple"' : '';

        $this->html = '<input '.$multiple.' type="file" name="'.$this->name.'"';

        $this->addOptions();

        $this->html .= (!isset($this->options['id'])) ? ' id="'.$this->name.'"' : '';

        $this->html .= '>';

        return $this->html;
    }

    /**
     * Eventuellement ajouter des options à la balise input.
     */
    private function addOptions(): void
    {
        if (count($this->options) > 0) {
            foreach ($this->options as $key => $value) {
                if ($key !== 'multiple') {
                    if (!in_array($key, self::OPTIONS_KEYS_ALLOWED)) {
                        Helper::getException('Key "'.$key.'" not authorized.');
                    } else {
                        $this->html .= ' '.$key.'="'.$value.'"';
                    }
                }
            }
        }
    }
}
