<?php

namespace DamianPhp\Auth;

use DamianPhp\Support\Facades\Cookie;
use DamianPhp\Support\Facades\Session;
use DamianPhp\Support\Facades\Response;
use DamianPhp\Support\Facades\Security;
use DamianPhp\Contracts\Auth\IsConnectedInterface;

/**
 * Classe client.
 *
 * Controller l'autorisation de l'accès à un espace membres.
 * - Vérifier que visiteur est bien un membre authentifié.
 *
 * Dans middleware :
 * - Il faut créer une instance : $isConnected = new IsConnected('App\Models\UserClassName');
 * - 3 méthodes sont obligatoires : session('AuthSessionName', [array assosiatif comme value]), redirectIfFalse('url_logout'), isLogged()
 * - 1 méthode est optionelle : cookie('cookie_name_de_remember_a_verif')
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class IsConnected implements IsConnectedInterface
{
    /**
     * Pour instancier classe du Model User.
     */
    private string $userModelInstance;

    /**
     * Nom de la session qu'on veut tester si elle existe.
     */
    private string $sessionName;

    /**
     * Valeur qu'on va donner à la session. Servira aussi à récupérer colonnes avec une req select. Servira aussi à tester session.
     */
    private array $sessionValue = [];

    /**
     * OPTIONAL - Pour éventuellemnt forcer le typage en int de certaines valeurs pour lors de la regéneration de la Session.
     */
    private array $valuesIntForRegenerateSession = [];

    /**
     * OPTIONAL - Eventuellemnt tester si un cookie remember.
     */
    private string $cookieName;

    /**
     * URL où rediriger l'user si isLogged return false.
     */
    private string $urlToredirectIfFalse;

    /**
     * @param string $modelName - Model où faire les requetes SQL.
     */
    public function __construct(string $modelName)
    {
        $this->userModelInstance = $modelName;
    }

    /**
     * @param string $sessionName - Nom de la session.
     * @param array $sessionValue - Valeur de la session sous forme de array numéroté.
     */
    public function session(string $sessionName, array $sessionValue, array $valuesIntForRegenerateSession = []): self
    {
        $this->sessionName = $sessionName;

        $this->sessionValue = $sessionValue;

        $this->valuesIntForRegenerateSession = $valuesIntForRegenerateSession;

        return $this;
    }

    /**
     * OPTIONAL (utile uniquement si on laisse un "Se souvenir de moi" au login).
     *
     * @param string $cookieName - Nom du cookie.
     */
    public function cookie(string $cookieName): self
    {
        $this->cookieName = $cookieName;

        return $this;
    }

    /**
     * @param string $urlToredirectIfFalse - URL de redirection si IsLogged return false.
     */
    public function urlToredirectIfFalse(string $urlToredirectIfFalse): self
    {
        $this->urlToredirectIfFalse = $urlToredirectIfFalse;

        return $this;
    }

    /**
     * Tester si l'user est identifié ou non.
     */
    public function isLogged(): bool
    {
        // il a le Cookie et pas la Session
        if ($this->cookieName !== null) {
            if (Cookie::has($this->cookieName) && !Session::has($this->sessionName)) {
                return $this->verifyCookie();
            }
        }

        // il a la Session
        if (Session::has($this->sessionName) && $this->hasKeysSession()) {
            return $this->verifySession();
        }

        // il n'a ni la Session et ni le Cookie
        return false;
    }

    /**
     * Vérifier que toutes les keys de la session existent.
     */
    private function hasKeysSession(): bool
    {
        foreach ($this->sessionValue as $sessionKey) {
            if (!isset(Session::get($this->sessionName)[$sessionKey])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Si cookie et pas de session -> vérifier si cookie est égale à celui dans BDD de l'user.
     */
    private function verifyCookie(): bool
    {
        $cookieRemember = Cookie::get($this->cookieName);
        $partsCookie = explode('==', $cookieRemember);
        $id = $partsCookie[0];

        $fieldsSelect = implode(', ', $this->sessionValue); // colonnes à récupérer depuis la BDD
        $dataUser = $this->userModelInstance::load()
            ->select($fieldsSelect.', remember_token')
            ->where('id', '=', $id)
            ->find();

        if ($dataUser) {
            $cookieExpectedWithKeyInBdd = $id.'=='.Security::hash($dataUser->remember_token);

            // si "remember_token" dans BDD est égale à key du cookie remember -> recréer la session de PHP et reprolonger le cookie
            if ($cookieExpectedWithKeyInBdd === $cookieRemember) {
                $this->regenerateSessionAndCookie($dataUser, $cookieRemember);

                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * Regénérer la Session et le Cookie.
     *
     * @param mixed $dataUser - Resultat d'un Model (1 seule ligne).
     */
    private function regenerateSessionAndCookie(mixed $dataUser, string $cookieRemember): void
    {
        // créer un array associatif - pour récupérer les colonnes voulus à mettre dans valeur de session
        $valueForSession = [];
        foreach ($this->sessionValue as $value) {
            $valueForSession[$value] = in_array($value, $this->valuesIntForRegenerateSession)
                ? (int) $dataUser->$value
                : $dataUser->$value;
        }

        Session::put($this->sessionName, $valueForSession);

        Cookie::put($this->cookieName, $cookieRemember);
    }

    /**
     * Verifie si la session existe.
     */
    private function verifySession(): bool
    {
        $dataUser = $this->userModelInstance::load()
            ->select('id')
            ->where('id', '=', Session::get($this->sessionName)['id'])
            ->find();
        
        if (!$dataUser) {
            return false;
        }

        return true;
    }

    /**
     * Exit.
     */
    public function exit()
    {
        if (Cookie::has($this->cookieName)) {
            Cookie::destroy($this->cookieName);
        }

        Response::redirect($this->urlToredirectIfFalse);
    }
}
