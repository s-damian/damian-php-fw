<?php

declare(strict_types=1);

namespace DamianPhp\Validation;

use DamianPhp\Contracts\Validation\ValidatorInterface;

/**
 * Pour retourner des string au format HTML.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class HtmlRenderer implements RendererInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return string - Les erreurs à retourner.
     */
    public function getErrors(): string
    {
        $html = '';

        if (! $this->validator->isValid()) {
            $html .= '<ul>';
            foreach ($this->validator->getErrors() as $error) {
                $html .= '<li>'.$error.'</li>';
            }
            $html .= '</ul>';
        }

        return $html;
    }

    /**
     * @return string - Le message de confirmation.
     */
    public function getSuccess(): string
    {
        $html = '';

        if ($this->validator->isValid()) {
            $html .= '<ul>';
            $html .=     '<li>'.$this->validator->getSuccess().'</li>';
            $html .= '</ul>';
        }

        return $html;
    }
}
