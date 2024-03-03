<?php

declare(strict_types=1);

namespace DamianPhp\Form\Generators;

use DamianPhp\Support\Helper;

/**
 * Pour générer un label.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class LabelGenerator
{
    private const OPTIONS_KEYS_ALLOWED = ['id', 'class', 'style'];

    private string $html;

    private string $for;

    private string $text;

    private array $options = [];

    /**
     * @param string $for - Pour faire référence à l'id de l'input auquel il fait référence.
     * @param string $text - Texte du label à aficher.
     * @param array $options - Pour éventuellement ajouter au label id, class css.
     */
    public function __construct(string $for, string $text, array $options = [])
    {
        $this->for = $for;
        $this->text = $text;
        $this->options = $options;
    }

    public function get(): string
    {
        $this->html = '<label for="'.$this->for.'"';

        $this->addOptions();

        $this->html .= '>'.$this->text.'</label>';

        return $this->html;
    }

    /**
     * Eventuellement ajouter des options à la balise input.
     */
    private function addOptions(): void
    {
        if (count($this->options) > 0) {
            foreach ($this->options as $key => $value) {
                if (! in_array($key, self::OPTIONS_KEYS_ALLOWED)) {
                    Helper::getException('Key "'.$key.'" not authorized.');
                } else {
                    $this->html .= ' '.$key.'="'.$value.'"';
                }
            }
        }
    }
}
