<?php

namespace DamianPhp\Form\Generators;

use DamianPhp\Support\Helper;

/**
 * Pour générer un textarea.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class TextareaGenerator
{
    use GeneratorTrait;

    private string $html;

    private string $name;

    private string $value;

    private array $options = [];

    private const OPTIONS_KEYS_ALLOWED = ['id', 'class', 'placeholder', 'style'];

    /**
     * @param string $name - Name du textarea.
     * @param string $value - Valeur du textarea
     * @param array $options - Pour éventuellement ajouter au label id, class css, placeholder...
     */
    public function __construct(string $name, ?string $value, array $options = [])
    {
        $this->name = $name;
        $this->value = $value;
        $this->options = $options;
    }

    public function get(): string
    {
        $this->html = '<textarea name="'.$this->name.'"'.$this->getRequired($this->options);

        $this->addOptions();

        $this->html .= !isset($this->options['id']) ? ' id="'.$this->name.'"' : '';

        $this->html .= '>';

        $this->html .= $this->getValueString('textarea', $this->name, $this->value);

        $this->html .= '</textarea>';

        return $this->html;
    }

    /**
     * Eventuellement ajouter des options à la balise input.
     */
    private function addOptions(): void
    {
        if (count($this->options) > 0) {
            foreach ($this->options as $k => $v) {
                if ($k !== 'required') {
                    if (!in_array($k, self::OPTIONS_KEYS_ALLOWED)) {
                        Helper::getException('Key "'.$k.'" not authorized.');
                    } else {
                        $this->html .= ' '.$k.'="'.$v.'"';
                    }
                }
            }
        }
    }
}
