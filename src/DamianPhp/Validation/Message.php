<?php

namespace DamianPhp\Validation;

use DamianPhp\Contracts\Validation\ValidatorInterface;

/**
 * Pour retourner les messages du Validator.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Message
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }

    /**
     * @return string - La réponse au format HTML.
     */
    public function toHtml(): string
    {
        $htmlRenderer = new HtmlRenderer($this->validator);

        if ($this->validator->isValid()) {
            return $htmlRenderer->getSuccess();
        }

        return $htmlRenderer->getErrors();
    }

    /**
     * @return string - La réponse au format JSON.
     */
    public function toJson(): string
    {
        $jsonRenderer = new JsonRenderer($this->validator);

        if ($this->validator->isValid()) {
            return $jsonRenderer->getSuccess();
        }

        return $jsonRenderer->getErrors();
    }
}
