<?php

declare(strict_types=1);

namespace DamianPhp\Contracts\Form;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface FormInterface
{
    /**
     * Ouvrir un formulaire.
     *
     * @param array $options
     * - $options['action'] string - Pour éventuellement préciser l'URL de l'action.
     * - $options['method'] string - Pour éventuellement préciser la méthode HTTP (POST par defaut).
     * - $options['files'] string - Pour éventuellement si il y a un système d'upload dans le form.
     * - $options['on_submit'] string - Pour éventuellement ex. : "Etes vous sur de vouloir effectuer cette action... ?".
     * - $options['id'] string - Pour éventuellement ajouter un id au formulaire.
     * - $options['css'] string - Pour éventuellement ajouter une class CSS au formulaire.
     * - $options['style'] string - Pour éventuellement mettre du style CSS.
     */
    public function open(array $options = []): string;

    /**
     * Générer un label.
     *
     * @param string $for - Pour faire référence à l'id de l'input auquel il fait référence.
     * @param string $text - Texte du label à aficher.
     * @param array $options - Pour éventuellement ajouter au label id, class css.
     */
    public function label(string $for, string $text, array $options = []): string;

    /**
     * Générer un input de type "text".
     */
    public function text(string $name, ?string $value, array $options = []): string;

    /**
     * Générer un input de type "email".
     */
    public function email(string $name, ?string $value, array $options = []): string;

    /**
     * Générer un input de type "search".
     * @return string
     */
    public function search(string $name, ?string $value, array $options = []): string;

    /**
     * Générer un input de type "url".
     */
    public function url(string $name, ?string $value, array $options = []): string;

    /**
     * Générer un input de type "tel".
     */
    public function tel(string $name, ?string $value, array $options = []): string;

    /**
     * Générer un input de type "password".
     */
    public function password(string $name, array $options = []): string;

    /**
     * Générer un input de type "hidden".
     */
    public function hidden(string $name, ?string $value, array $options = []): string;

    /**
     * Générer un input de type "checkbox".
     */
    public function checkbox(string $name, ?string $value, array $options = []): string;

    /**
     * Générer un input de type "radio".
     */
    public function radio(string $name, ?string $value, array $options = []): string;

    /**
     * Générer un input de type "number".
     */
    public function number(string $name, ?string $value, array $options = []): string;

    /**
     * Générer un input de type "range".
     */
    public function range(string $name, ?string $value, array $options = []): string;

    /**
     * Générer un input de type "submit".
     */
    public function submit(string $name = 'submit', ?string $value = null, array $options = []): string;

    /**
     * Générer un input de type "file".
     */
    public function file(string $name, array $options = []): string;

    /**
     * Générer un button.
     *
     * @param $value string|null - Texte à affiche dans le button.
     * @param array $options - Pour éventuellement ajouter au label id, class css.
     */
    public function button(?string $value = null, array $options = []): string;

    /**
     * @param string $name - name du textarea.
     * @param string $value - Valeur du textarea.
     * @param array $options - Pour éventuellement ajouter au label id, class css, placeholder...
     */
    public function textarea(string $name, ?string $value, array $options = []): string;

    /**
     * Générer un <select> avec des <option>
     * Et aussi éventuellement avec des <optgroup>
     *
     * @param string $name - name du <select>
     * @param array $balisesOption - les <option>
     * @param string|int|null $selectedPerDefault - éventuellement ajouter un selected active par default.
     * @param array $options - pour éventuellement ajouter au select : id, class, style, autosubmit, une option disabled (pour simuler un placeholder).
     */
    public function select(string$name, array $balisesOption, $selectedPerDefault = null, array $options = []): string;

    /**
     * Fermer un formulaire.
     */
    public function close(): string;
}
