<?php

namespace DamianPhp\Form\Generators;

use DamianPhp\Support\Helper;

/**
 * Pour générer un input.
 *
 * Pour générer un input de type :
 * text, email, search, url, tel,
 * password, hidden, checkbox, radio, file, submit,
 * number, range
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class InputGenerator
{
    use GeneratorTrait;

    private string $html;

    private string $type;

    private string $name;

    private ?string $value;

    private array $options = [];

    /**
     * @param string $type - Type de l'input.
     * @param string $name - Name de l'input.
     * @param string $value - Value de l'input.
     * @param array $options - Pour éventuellement ajouter au label id, class css, placeholder...
     */
    public function __construct(string $type, string $name, ?string $value, array $options = [])
    {
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        $this->options = $options;
    }

    public function get(): string
    {
        $checked = $this->type === 'checkbox' && isset($this->options['checked']) && $this->options['checked'] === true
            ? ' checked '
            : '';

        $valueInput = 'value="'.$this->getValueString($this->type, $this->name, $this->value).'"';
        $this->html = '<input type="'.$this->type.'" name="'.$this->name.'" '.$valueInput.' '.$this->getRequired($this->options).$checked;

        $this->addOptions();

        $this->html .= !isset($this->options['id']) ? ' id="'.$this->name.'"' : '';

        $this->html .= '>';

        return $this->html;
    }

    /**
     * Eventuellement ajouter des options à la balise input.
     */
    private function addOptions(): void
    {
        if (count($this->options) > 0) {
            switch ($this->type) {
                case 'text': case 'email': case 'search': case 'url': case 'tel': case 'password':
                    $keysAllowed = ['id', 'class', 'style', 'placeholder', 'autocomplete'];
                    break;
                case 'hidden': case 'checkbox': case 'radio': case 'file': case 'submit':
                    $keysAllowed = ['id', 'class', 'style'];
                    break;
                case 'number': case 'range':
                    $keysAllowed = ['id', 'class', 'style', 'step', 'min', 'max'];
                    break;
                default:
                    $keysAllowed = [];
                    Helper::getException('Type "'.$this->type.'" not exists.');
                    break;
            }

            foreach ($this->options as $k => $v) {
                if ($k !== 'required' && $k !== 'checked') {
                    if (!in_array($k, $keysAllowed)) {
                        Helper::getException('Key "'.$k.'" not authorized.');
                    } else {
                        $this->html .= ' '.$k.'="'.$v.'"';
                    }
                }
            }
        }
    }
}
