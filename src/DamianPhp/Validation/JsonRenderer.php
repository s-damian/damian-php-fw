<?php

namespace DamianPhp\Validation;

use DamianPhp\Support\Facades\Json;
use DamianPhp\Contracts\Validation\ValidatorInterface;

/**
 * Pour retourner des string au format JSON.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class JsonRenderer implements RendererInterface
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
        $json = '';

        if (! $this->validator->isValid()) {
            $json .= Json::encode($this->validator->getErrors());
        }

        return $json;
    }

    /**
     * @return string - Le message de confirmation.
     */
    public function getSuccess(): string
    {
        $json = '';

        if ($this->validator->isValid()) {
            $json .= Json::encode($this->validator->getSuccess());
        }

        return $json;
    }
}
