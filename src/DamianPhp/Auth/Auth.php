<?php

namespace DamianPhp\Auth;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Str;
use DamianPhp\Support\Facades\Date;
use DamianPhp\Support\Facades\Input;
use DamianPhp\Support\Facades\Cookie;
use DamianPhp\Support\Facades\Session;
use DamianPhp\Support\Facades\Security;
use DamianPhp\Contracts\Auth\AuthInterface;

/**
 * Classe client.
 *
 * Autentification.
 * - Connection de l'utilisateur.
 * - Eventuellement laisser la possibilitée aux utilisateurs d'avoir une connexion perraine avec un système de cookie.
 *
 * ********** OBLIGATOIRE pour chaque création d'espace membres **********
 * Dans la table user de la BDD :
 * - Il doit y avoir : un champ VARCHAR "remember_token", un champ Timestamp "date_last_connexion"
 *
 * Dans le Controller d'authentification :
 * - Il faut créer une instance : $auth = new Auth('App\Models\UserClassName');
 * - OPTIONAL - Si on veut laisser possibilitée d'une connexion perraine, préciser le nom du cookie avec la méthode remember('cookie_name_remember')
 * - Préciser le nom de la session d'auth avec sa valeur avec la méthode connect('AuthSessionName', [array assosiatif comme value])
 *
 * Dans la vue de login :
 * - Si on met une case à cocher, elle doit toujours avoir : name="remember"
 * ********** /OBLIGATOIRE pour chaque création d'espace membres **********
 *
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Auth implements AuthInterface
{
    /**
     * Pour instancier classe du Model User.
     */
    private string $userModelInstance;

    /**
     * Nom du cookie remember.
     */
    private string $cookieNameRemember;

    public function __construct(string $modelName)
    {
        $this->userModelInstance = $modelName;
    }

    /**
     * OPTIONAL
     * Pour éventuellement laisser la possibilitée aux users d'avoir une connexion perraine.
     */
    public function remember(string $cookieNameRemember): self
    {
        $this->cookieNameRemember = $cookieNameRemember;

        return $this;
    }

    /**
     * Connexion de l'user.
     *
     * @param string $sessionName - Nom de la session Auth.
     * @param array $valuesSession - Valeurs à envoyer à la session.
     */
    public function connect(string $sessionName, array $valuesSession): self
    {
        $id = $valuesSession['id'];

        // requete SQL UPDATE pour modifier la date de dernière connexion
        $this->userModelInstance::load()
            ->where('id', '=', $id)
            ->limit(1)
            ->update(['date_last_connexion' => Date::getDateTimeFormat()]);

        if (Helper::config('app')['env'] !== 'testing') {
            Session::regenerateId(); // sécuritée -> ne pas avoir le même session id au début et au moment où on s'identifie
        }

        Session::put($sessionName, $valuesSession);

        if (Input::hasPost('remember') && Input::post('remember') === 'on' && $this->cookieNameRemember !== null) {
            $this->rememberMe($id);
        }

        return $this;
    }

    /**
     * Pour si l'option "se souvenir de moi" est cochée.
     *
     * On hash le cookie dans le $_COOKIE, mais on ne le hash pas dans la BDD dans la colonne 'remember_token' de l'user.
     * Comme ça, si le pirate n'a pas le salt de cryptage, il ne poura pas faire le test d'égalité.
     *
     * @param int $id - ID de l'user.
     */
    private function rememberMe(int $id): void
    {
        $rememberKey = Str::random(64);

        Cookie::put($this->cookieNameRemember, $id.'=='.Security::hash($rememberKey));

        // requete SQL UPDATE pour mettre la cookie dans la BDD WHERE user
        $this->userModelInstance::load()->where('id', '=', $id)->limit(1)->update(['remember_token' => $rememberKey]);
    }
}
