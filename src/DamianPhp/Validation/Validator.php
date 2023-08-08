<?php

namespace DamianPhp\Validation;

use DateTime;
use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Str;
use DamianPhp\Http\Request\Request;
use DamianPhp\Support\Facades\Date;
use DamianPhp\Support\Facades\Hash;
use DamianPhp\Support\Facades\Slug;
use DamianPhp\Support\Facades\Input;
use DamianPhp\Contracts\Validation\ValidatorInterface;

/**
 * Classe client.
 * Pour les vérifications des données.
 *
 * # Fonctionnement de ce package :
 * Pour générer les réponses,
 * la classe "Validator" fait appelle à la classe "Message"
 * qui fait appelle à un Renderer ("HtmlRenderer", ou "JsonRenderer")
 * et retournera la réponse (success ou erreur(s)).
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Validator implements ValidatorInterface
{
    public const REGEX_CHARACTERS_PROHIBITED_NAME_FILE = '/[\/:*?"<>|\\\\ ]/';

    private const REGEX_TEL = '/^[0-9-+(),;._ \/]{4,20}$/';

    private const REGEX_SLUG = '/^[a-z0-9\-]+$/';

    private const REGEX_ALPHA = '/^[a-z]+$/i';

    private const REGEX_INTEGER = '/^[0-9]+$/';

    private const REGEX_ALPHA_NUMERIC = '/^[a-z0-9]+$/i';

    private const REGEX_DATE_TIME = '#^\d{2}/\d{2}/\d{4} \d{2}:\d{2}$#';

    private const REGEX_DATE = '#^\d{2}/\d{2}/\d{4}$#';

    private const REGEX_POSTALE_CODE = '/^[0-9]{5}$/';

    private Request $request;

    /**
     * $_POST ou $_GET - Sera $_POST par defaut.
     */
    private array $requestMethod;

    /**
     * Pour éventuellement personnaliser certains attributs de validation.
     */
    private string $label;

    /**
     * Name du input.
     */
    private string $input;

    /**
     * Valeur des rules qu'on passe à un input.
     */
    private mixed $value;

    /**
     * Attributs de validation personnalisés.
     */
    private array $labels = [];

    /**
     * Contiendra les éventuelles erreurs.
     */
    private array $errors = [];

    /**
     * Contiendra les messages d'erreurs à remplacer.
     */
    private array $messageErrorsSpecified = [];

    /**
     * Les éventuels règles da validation pour un traitement spécifique.
     */
    private static array $extends = [];

    public function __construct(array $requestMethod = [])
    {
        $this->request = new Request();

        $this->requestMethod = $requestMethod !== [] ? $requestMethod : $this->request->getPost()->all();

        $this->labels = Helper::lang('validation')['labels'];
    }

    /**
     * Pour ajouter une règle de validation.
     */
    public static function extend(string $rule, callable $callable): void
    {
        self::$extends[$rule] = $callable;
    }

    /**
     * Activer le validateur.
     */
    public function rules(array $inputsWithRules): void
    {
        foreach ($inputsWithRules as $input => $rules) {
            $this->input = $input;

            if (is_array($rules)) {
                $this->setLabel($rules);

                foreach ($rules as $rule => $value) {
                    if ($rule !== 'label') {
                        if ($rule === 'required' || $rule === 'file' || isset($this->requestMethod[$this->input])) {
                            $this->value = $value;

                            $this->callRule($rule);
                        }
                    }
                }
            }
        }
    }

    public function specifyMessageErrors(array $messages): void
    {
        foreach ($messages as $input => $message) {
            $this->messageErrorsSpecified[$input] = $message;
        }
    }

    private function setLabel(array $rules): void
    {
        if (isset($rules['label'])) {
            $this->label = $rules['label'];
        } elseif (array_key_exists($this->input, $this->labels)) {
            $this->label = $this->labels[$this->input];
        } else {
            $this->label = ucfirst($this->input);
        }
    }

    /**
     * Appeler la règle de validation.
     */
    private function callRule(string $rule): void
    {
        $methodVerify = 'verify'.Str::convertSnakeCaseToCamelCase($rule);

        if (method_exists($this, $methodVerify)) {
            $this->$methodVerify();
        } else {
            if (! array_key_exists($rule, self::$extends)) {
                Helper::getExceptionOrLog('Rule "'.$rule.'" not exist.');
            }

            $this->ruleWithExtend($rule);
        }
    }

    private function ruleWithExtend(string $rule): void
    {
        if (self::$extends[$rule]($this->input, $this->requestMethod[$this->input], $this->value) === false) {
            if (! array_key_exists($rule, Helper::lang('validation'))) {
                Helper::getExceptionOrLog('Error response "'.$rule.'" not exist.');
            }

            $this->errors[$this->input] = $this->pushError($rule);
        }
    }

    /**
     * Vérifier que la valeur soumise dans le champ est bien alphabétique.
     */
    private function verifyAlpha(): void
    {
        if ($this->value === true && ! preg_match(self::REGEX_ALPHA, $this->requestMethod[$this->input])) {
            $this->errors[$this->input] = $this->pushError('alpha');
        }
    }

    /**
     * Vérifier que la valeur soumise dans le champ est bien alphanumérique.
     */
    private function verifyAlphaNumeric(): void
    {
        if ($this->value === true && ! preg_match(self::REGEX_ALPHA_NUMERIC, $this->requestMethod[$this->input])) {
            $this->errors[$this->input] = $this->pushError('alpha_numeric');
        }
    }

    /**
     * Vérifier que la valeur est entrée entre 2 valeurs spécifiées.
     * $this->value - (array numeroté) Valeur doit être entre $this->value[0] (valeur min) et $this->value[1] (valeur max).
     */
    private function verifyBetween(): void
    {
        if ($this->requestMethod[$this->input] < $this->value[0] || $this->requestMethod[$this->input] > $this->value[1]) {
            $this->errors[$this->input] = $this->pushError('between', $this->value);
        }
    }

    /**
     * Pour obliger 2 valeurs à êtres égales.
     */
    private function verifyConfirm(): void
    {
        if ($this->value[0] !== $this->value[1]) {
            $this->errors[$this->input] = $this->pushError('confirm');
        }
    }

    /**
     * Verifier que date entrée dans input text n'est pas après la date courante.
     */
    private function verifyDateTimeNotAfterNow(): void
    {
        $dateInput = new DateTime(
            DateTime::createFromFormat('d/m/Y H:i', $this->requestMethod[$this->input])->format('Y-m-d H:i:s')
        );

        $dateNow = new DateTime(Date::getDateTimeFormat());

        if ($dateInput > $dateNow) {
            $this->errors[$this->input] = $this->pushError('date_time_not_after_now');
        }
    }

    /**
     * Champ doit obligatoirement rester vide.
     */
    private function verifyEmpty(): void
    {
        if ($this->requestMethod[$this->input] !== '') {
            $this->errors[$this->input] = $this->pushError('empty');
        }
    }

    /**
     * Vérifier que la valeur soumise est bien au format d'une date.
     */
    private function verifyFormatDate(): void
    {
        if ($this->requestMethod[$this->input] !== '') {
            if ($this->value === true && ! preg_match(self::REGEX_DATE, $this->requestMethod[$this->input])) {
                $this->errors[$this->input] = $this->pushError('format_date');
            }
        }
    }

    /**
     * Vérifier que la valeur soumise est bien au format d'une date/heure.
     */
    private function verifyFormatDateTime(): void
    {
        if ($this->requestMethod[$this->input] !== '') {
            if ($this->value === true && ! preg_match(self::REGEX_DATE_TIME, $this->requestMethod[$this->input])) {
                $this->errors[$this->input] = $this->pushError('format_date_time');
            }
        }
    }

    /**
     * Vérifier que la valeur soumise est bien au format d'une adresse mail.
     */
    private function verifyFormatEmail(): void
    {
        if ($this->requestMethod[$this->input] !== '') {
            if ($this->value === true && ! filter_var($this->requestMethod[$this->input], FILTER_VALIDATE_EMAIL) === true) {
                $this->errors[$this->input] = $this->pushError('format_email');
            }
        }
    }

    /**
     * Verifier que valeur soumise est bien au format d'une adresse IP.
     */
    private function verifyFormatIp(): void
    {
        if ($this->requestMethod[$this->input] !== '') {
            if ($this->value === true && ! filter_var($this->requestMethod[$this->input], FILTER_VALIDATE_IP)) {
                $this->errors[$this->input] = $this->pushError('format_ip');
            }
        }
    }

    /**
     * Verifier que valeur soumise est bien au format d'un nom de fichier.
     */
    private function verifyFormatNameFile(): void
    {
        if ($this->requestMethod[$this->input] !== '') {
            if (
                $this->value === true &&
                preg_match(self::REGEX_CHARACTERS_PROHIBITED_NAME_FILE, $this->requestMethod[$this->input])
            ) {
                $this->errors[$this->input] = $this->pushError('format_name_file');
            }
        }
    }

    /**
     * Verifier que valeur soumise est bien au format d'un code postale.
     */
    private function verifyFormatPostalCode(): void
    {
        if ($this->requestMethod[$this->input] !== '') {
            if ($this->value === true && ! preg_match(self::REGEX_POSTALE_CODE, $this->requestMethod[$this->input])) {
                $this->errors[$this->input] = $this->pushError('format_postal_code');
            }
        }
    }

    /**
     * Vérifier que la valeur soumise est bien au format d'un d'un slug.
     */
    private function verifyFormatSlug(): void
    {
        if ($this->requestMethod[$this->input] !== '') {
            if ($this->value === true && ! preg_match(self::REGEX_SLUG, $this->requestMethod[$this->input])) {
                $this->errors[$this->input] = $this->pushError('format_slug');
            }
        }
    }

    /**
     * Vérifier que la valeur soumise est bien au format d'un numéro de téléphone.
     */
    private function verifyFormatTel(): void
    {
        if ($this->requestMethod[$this->input] !== '') {
            if ($this->value === true && ! preg_match(self::REGEX_TEL, $this->requestMethod[$this->input])) {
                $this->errors[$this->input] = $this->pushError('format_tel');
            }
        }
    }

    /**
     * Vérifier que la valeur soumise est bien au format d'une URL.
     */
    private function verifyFormatUrl(): void
    {
        if ($this->requestMethod[$this->input] !== '') {
            if ($this->value === true && ! filter_var($this->requestMethod[$this->input], FILTER_VALIDATE_URL)) {
                $this->errors[$this->input] = $this->pushError('format_url');
            }
        }
    }

    /**
     * Vérifier que la valeur soumise dans le champ est bien un entier.
     */
    private function verifyInteger(): void
    {
        if ($this->value === true && ! preg_match(self::REGEX_INTEGER, $this->requestMethod[$this->input])) {
            $this->errors[$this->input] = $this->pushError('integer');
        }
    }

    /**
     * Vérifier si donnée envoyés est dans un array.
     */
    private function verifyInArray(): void
    {
        if (! in_array($this->requestMethod[$this->input], $this->value)) {
            $this->errors[$this->input] = $this->pushError('in_array');
        }
    }

    /**
     * Nombre de caractères maximum autorisés dans champ.
     */
    private function verifyMax(): void
    {
        if (mb_strlen($this->requestMethod[$this->input]) > $this->value) {
            $this->errors[$this->input] = $this->pushError('max', $this->value);
        }
    }

    /**
     * Nombre de caractères minimum imposés dans champ.
     */
    private function verifyMin(): void
    {
        if (mb_strlen($this->requestMethod[$this->input]) < $this->value) {
            $this->errors[$this->input] = $this->pushError('min', $this->value);
        }
    }

    /**
     * Vérifier dans un répertoire spécifié, qu'un dossier n'a pas déjà le même nom que le dossier qu'on ajout ou que l'on modifie.
     */
    private function verifyNameDirectoryUniqueInDirectory(): void
    {
        $listRep = scandir($this->value['path']);
        $directoryExcluded = $this->value['directory_excluded'] ?? null;

        foreach ($listRep as $file) {
            if ($this->requestMethod[$this->input] === $file && $file !== $directoryExcluded) {
                $error = true;

                break;
            }
        }

        if (isset($error)) {
            $this->errors[$this->input] = $this->pushError('name_directory_unique_in_directory');
        }
    }

    /**
     * Vérifier dans un répertoire spécifié, qu'un fichier n'a pas déjà le meme nom que le fichier qu'on ajout ou que l'on modifie.
     */
    private function verifyNameFileUniqueInDirectory(): void
    {
        $listRep = scandir($this->value['path']);
        $fileExcluded = $this->value['file_excluded'] ?? null;

        foreach ($listRep as $file) {
            if ($this->requestMethod[$this->input] === $file && $file !== $fileExcluded) {
                $error = true;

                break;
            }
        }

        if (isset($error)) {
            $this->errors[$this->input] = $this->pushError('name_file_unique_in_directory');
        }
    }

    /**
     * Verifier que valeur soumise n'est pas au format d'un regex spécifique.
     */
    private function verifyNoRegex(): void
    {
        if (preg_match($this->value, $this->requestMethod[$this->input])) {
            $this->errors[$this->input] = $this->pushError('no_regex', $this->value);
        }
    }

    /**
     * Vérifier si donnée envoyés n'est pas dans un array.
     */
    private function verifyNotInArray(): void
    {
        if (in_array($this->requestMethod[$this->input], $this->value)) {
            $this->errors[$this->input] = $this->pushError('not_in_array');
        }
    }

    /**
     * Pour vérifier si password entré est === à password qui est dans BDD.
     *
     * $this->value
     * - $this->value['model'] : Nom du Model à instancier.
     * - $this->value['column'] : Colonnes à SELECT (pour tester l'égalité).
     * - $this->value['where'] : WHERE dans requete SQL.
     * - $this->value['password_to_verify'] : Password envoyée en POST à verifier si il egale à champs qui est dans BDD.
     */
    private function verifyPasswordCurrentOk(): void
    {
        $model = new $this->value['model']();

        $result = $model->select($this->value['column'])->where($this->value['where'])->find();

        $passwordInBdd = $this->value['column'];

        if (! Hash::verify($this->value['password_to_verify'], $result->$passwordInBdd)) {
            $this->errors[$this->input] = $this->pushError('password_current_ok');
        }
    }

    /**
     * Verifier que valeur soumise est bien au format d'un regex spécifique.
     */
    private function verifyRegex(): void
    {
        if (! preg_match($this->value, $this->requestMethod[$this->input])) {
            $this->errors[$this->input] = $this->pushError('regex', $this->value);
        }
    }

    /**
     * Champ doit obligatoirement etre remplis.
     */
    private function verifyRequired(): void
    {
        if (
            ($this->value === true && ! array_key_exists($this->input, $this->requestMethod)) or
            $this->requestMethod[$this->input] === ''
        ) {
            $this->errors[$this->input] = $this->pushError('required');
        }
    }

    /**
    * Vérifer si unique dans la BDD.
    *
    * $this->value - array associatif ->
    * - $this->value['model'] : Nom du Model à instancier.
    * - $this->value['where'] => WHERE
    */
    private function verifyUnique(): void
    {
        $model = new $this->value['model']();

        $result = $model->select('id')->where($this->value['where'])->find();

        if ($result) {
            $this->errors[$this->input] = $this->pushError('unique');
        }
    }

    /**
    * Vérifer si unique selon un array donné.
     */
    private function verifyUniqueNotInArray(): void
    {
        if (in_array($this->requestMethod[$this->input], $this->value)) {
            $this->errors[$this->input] = $this->pushError('unique_not_in_array');
        }
    }

    /**
    * Si input vide, vérifer si unique (avec un format de slug) selon un array donné.
     */
    private function verifyUniqueNotInArrayWithSlug(): void
    {
        if (empty($this->requestMethod[$this->input])) {
            if (in_array(Slug::create($this->requestMethod[$this->value[1]]), $this->value[0])) {
                $this->errors[$this->input] = $this->pushError('unique_not_in_array');
            }
        }
    }

    /**
     * Pour vérifier si champ entrée est égal à la valeur d'un champ qui est dans BDD.
     *
     * $this->value
     * - $this->value['model'] : Nom du Model à instancier.
     * - $this->value['column'] : Colonnes à SELECT (pour tester l'égalité).
     * - $this->value['where'] : WHERE dans requete SQL
     * - $this->value['data_to_verify'] : Donnée envoyée en POST à vérifier si égale à champs qui est dans BDD.
     * - $this->value['error_message'] : Message d'erreur.
     */
    private function verifyVerifyWithDbData(): void
    {
        $model = new $this->value['model']();

        $result = $model->select($this->value['column'])->where($this->value['where'])->find();

        $dbColumn = $this->value['column'];

        if (! isset($result->$dbColumn) || $result->$dbColumn !== $this->value['data_to_verify']) {
            $this->errors[$this->input] = $this->value['error_message'];
        }
    }

    /**
     * Si il y a une erreur -> pushera une erreur par input.
     *
     * @param string $key - Key dans le tableau inclut dans resources/lang...
     * @param null|array|string $value - Pour éventuellemnt {value} dans le tableau inclut dans resources/lang...
     */
    private function pushError(string $key, array|string $value = null): string
    {
        $errorMessage = str_replace('{field}', $this->label, Helper::lang('validation')[$key]);

        if ($value !== null) {
            if (is_array($value)) { // utile pour 'between'
                $i = 0;
                foreach ($value as $v_null) {
                    $errorMessage = str_replace('{value_'.$i.'}', $value[$i], $errorMessage);
                    $i++;
                }
            } else {
                $errorMessage = str_replace('{value}', $value, $errorMessage);
            }
        }

        return $this->messageErrorsSpecified[$this->input][$key] ?? $errorMessage;
    }

    /**
     * $this->value - Options possibles :
     *
     * - $this->value['name_not_taken'] -> Vérifier que le nom n'est pas déjà pris.
     * - $this->value['path'] -> Chemin du fichier uplodé pour si vérifier que le nom n'est pas déjà pris.
     *
     * - $this->value['max_length_name'] -> Eventuellemnt donner une limite max au nom du fichier.
     * - $this->value['specific_name'] -> Eventuellement obliger la specifidation d'un nom au fichier (pas pour multiple).
     *
     * - $this->value['max_size'] -> Eventuellemnt donner une limite max au poids du fichier.
     * - $this->value['extension'] -> Vérifier extension.
     * - $this->value['required'] -> Fichier obligatoirement requis.
     */
    private function verifyFile(): void
    {
        $fileValidation = new FileValidation($this);

        if (
            isset(Input::file($this->input)['name']) &&
            is_array(Input::file($this->input)['name']) &&
            Input::file($this->input)['name'][0] === ''
        ) {
            if (isset($this->value['required'])) { // vérifier qu'un fichier a été séléctionné avec upload multiple
                $this->errors[$this->input] = $fileValidation->pushErrorWithFile('required');
            }
        } else {
            if (Input::hasFile($this->input)) {
                if (is_array(Input::file($this->input)['name'])) {
                    $fileValidation->verifyMultipleFile($this->input, $this->value);
                } else {
                    $fileValidation->verifyOneFile($this->input, $this->value);
                }
            } else {
                if (isset($this->value['required'])) { // vérifier qu'un fichier a été séléctionné
                    if (Input::file($this->input) === '') {
                        $this->errors[$this->input] = $fileValidation->pushErrorWithFile('required');
                    }
                }
            }
        }
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function addErrorWithInput(string $input, string $error): void
    {
        $this->errors[$input] = $error;
    }

    /**
     * Pour éventuellemnt ajouter des erreurs "à la volé" selon éventuels traitements.
     */
    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * @return bool - True si formulaire soumis est valide, false si pas valide.
     */
    public function isValid(): bool
    {
        return count($this->errors) === 0;
    }

    /**
     * Vérifier si un input à une erreur.
     *
     * @param string $key - Name de l'input
     * @return bool - True si input à au minimum une erreur.
     */
    public function hasError(string $key): bool
    {
        return isset($this->errors[$key]);
    }

    /**
     * @param string $key - Name de l'input.
     * @return string - Erreur(s) de l'input.
     */
    public function getError(string $key): string
    {
        return $this->hasError($key) ? $this->errors[$key] : '';
    }

    /**
     * @return array - Array associatif des erreurs.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return string - Le message de confirmation.
     */
    public function getSuccess(): string
    {
        return Helper::lang('validation')['success_message'];
    }

    public function getMessages(): Message
    {
        return new Message($this);
    }

    /**
     * @return string - Les erreurs à retourner au format HTML.
     */
    public function getErrorsHtml(): string
    {
        return $this->getMessages()->toHtml();
    }

    /**
     * @return string - Les erreurs à retourner au format JSON.
     */
    public function getErrorsJson(): string
    {
        return $this->getMessages()->toJson();
    }
}
