<?php

namespace DamianPhp\Contracts\Validation;

use DamianPhp\Validation\Message;

/**
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
Interface ValidatorInterface
{
    public function __construct(array $requestMethod = []);
    
    /**
     * Pour ajouter une règle de validation.
     */
    public static function extend(string $rule, callable $callable): void;
    
    /**
     * Activer le validateur.
     */
    public function rules(array $params): void;

    public function getLabel(): string;

    public function addErrorWithInput(string $input, string $error): void;

    /**
     * Pour éventuellemnt ajouter des erreurs "à la volé" selon éventuels traitements.
     */
    public function addError(string $error): void;

    /**
     * @return bool - True si formulaire soumis est valide, false si pas valide.
     */
    public function isValid(): bool;

    /**
     * Vérifier si un input à une erreur.
     *
     * @param string $key - Name de l'input
     * @return bool - True si input à au minimum une erreur.
     */
    public function hasError(string $key): bool;

    /**
     * @param string $key - Name de l'input.
     * @return string - Erreur(s) de l'input.
     */
    public function getError(string $key): string;

    /**
     * @return array - Array associatif des erreurs.
     */
    public function getErrors(): array;

    /**
     * @return string - Le message de confirmation.
     */
    public function getSuccess(): string;
    
    public function getMessages(): Message;

    /**
     * @return string - Les erreurs à retourner au format HTML.
     */
    public function getErrorsHtml(): string;

    /**
     * @return string - Les erreurs à retourner au format JSON.
     */
    public function getErrorsJson(): string;
}
