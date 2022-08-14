<?php

namespace DamianPhp\Support\Security;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Str;
use DamianPhp\Support\Facades\Flash;
use DamianPhp\Support\Facades\Input;
use DamianPhp\Support\Facades\Session;
use DamianPhp\Support\Facades\Response;
use DamianPhp\Contracts\Security\TokenInterface;
use DamianPhp\Support\Facades\Security as SecurityF;

/**
 * CSRF CsrfToken.
 * Peut fonctionner avec une Facade.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class CsrfToken implements TokenInterface
{
    /**
     * Si pas de session token -> lui ajouter.
     */
    public function addSession(): void
    {
        if (! Session::has('_token')) {
            $random = Str::random(35);
            
            Session::put('_token', SecurityF::hash($random));
        }
    }

    /**
     * A mettre dans HTML des form en POST.
     * class="token-post"
     */
    public function htmlPost(): string
    {
        return '<input type="hidden" class="token-post" name="_token" value="'.Session::get('_token').'">';
    }

    /**
     * A mettre dans traitements des form en POST.
     */
    public function verifyPost()
    {
        if (! Session::has('_token') || !Input::hasPost('_token') || (Input::post('_token') !== Session::get('_token'))) {
            Flash::setError(Helper::lang('security')['csrf_token_post']);

            return Response::redirect(Session::get('_url'));
        }
    }

    /**
     * A mettre dans liens en GET.
     */
    public function htmlGet(string $getOrAnd = '?'): string
    {
        return $getOrAnd.'_token='.Session::get('_token');
    }

    /**
     * A mettre dans traitements des form en GET.
     */
    public function verifyGet()
    {
        if (! Session::has('_token') || !Input::hasGet('_token') || (Input::get('_token') !== Session::get('_token'))) {
            Flash::setError(Helper::lang('security')['csrf_token_get']);

            return Response::redirect(Session::get('_url'));
        }
    }
}
