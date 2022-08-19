<?php

namespace DamianPhp\Http\Controller;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Flash;
use DamianPhp\Support\Facades\Response;

/**
 * Controller parent de App\Http\Controllers\Controller
 * qui est lui meme controller parent de tout les controllers de l'application
 *
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
abstract class BaseController
{
    /**
     * Pour éventuellement utiliser un autre template que celui par defaut.
     */
    private string $layout;

    /**
     * Pour éventuellement utiliser une autre extension que "php".
     */
    private string $extension;

    /**
     * Pour éventuellement ajouter via controller enfant CSS spécifique.
     */
    private array $addCss = [];

    /**
     * Pour éventuellement ajouter via controller enfant CSS spécifique.
     */
    private array $addJs = [];

    /**
     * Pour éventuellement ajouter des valeurs au $data à envoyer dans vues.
     */
    private array $addData = [];

    public function __construct()
    {
        $this->layout = 'default';
        $this->extension = 'php';
    }

    /**
     * Eventuellement utiliser un autre template que celui par defaut.
     */
    final protected function setLayout(string $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Eventuellement utiliser une autre extension que "php".
     */
    final protected function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Pour éventuellement ajouter via un Controller enfant un/des fichier(s) CSS spécifique(s).
     * Exemple : $this->addCss('name')->addCss('name2'); ou : $this->addCss(['name', 'name2']);
     */
    final protected function addCss(array|string $css): self
    {
        if (is_array($css)) {
            foreach ($css as $oneCss) {
                $this->addCss[] = $oneCss;
            }
        } else {
            $this->addCss[] = $css;
        }

        return $this;
    }

    /**
     * Pour éventuellement ajouter via un Controller enfant un/des fichier(s) JS spécifique(s).
     * Exemple : $this->addJs('name')->addJs('name2'); ou : $this->addJs(['name', 'name2']);
     */
    final protected function addJs(array|string $js): self
    {
        if (is_array($js)) {
            foreach ($js as $oneJs) {
                $this->addJs[] = $oneJs;
            }
        } else {
            $this->addJs[] = $js;
        }

        return $this;
    }

    /**
     * Pour éventuellement ajouter des valeurs au $data à envoyer dans vue.
     * Exemple : $this->addData(['key1' => 'value1'])->addData(['key2' => 'value2']); ou : $this->addData(['key1' => 'value1', 'key2' => 'value2']);
     */
    final protected function addData(array $data): self
    {
        $this->addData[] = $data;

        return $this;
    }

    /**
     * Retourner la vue.
     *
     * @param string $view - Fichier View à charger.
     * @param array $data - Pour renvoyer éventuels données à la vue.
     */
    final protected function view(string $view, array $data = []): never
    {
        if ($this->addData !== null) {
            foreach ($this->addData as $addOneData) {
                $data += $addOneData;
            }
        }

        if ($data) {
            extract($data);
        }

        if (isset($viewInLayout)) {
            Helper::getExceptionOrLog('Key "viewInLayout" cannot be declared.');
        }
        if (isset($specificsCss)) {
            Helper::getExceptionOrLog('Key "specificsCss" cannot be declared.');
        }
        if (isset($specificsJs)) {
            Helper::getExceptionOrLog('Key "specificsJs" cannot be declared.');
        }

        ob_start();
        require Helper::basePath('resources/views/'.$view.'.'.$this->extension);
        $viewInLayout = ob_get_clean();

        if ($this->addCss !== null) {
            foreach ($this->addCss as $addOneCss) {
                $specificsCss[] = $addOneCss;
            }
        }

        if ($this->addJs !== null) {
            foreach ($this->addJs as $addOneJs) {
                $specificsJs[] = $addOneJs;
            }
        }

        require Helper::basePath(Helper::config('path')['layouts'].'/'.$this->layout.'.php');

        exit();
    }

    /**
     * Instancier le(s) Middleware(s).
     *
     * @param array|string $key - Key passé(s) en param du Middleware dans Controller.
     */
    final protected function middleware(array|string $key): void
    {
        $kernelClass = 'App\Http\Kernel';
        new $kernelClass($key);
    }

    /**
     * Message flash de confirmation.
     *
     * @param string $message
     * @return $this
     */
    final protected function withOk(string $message): self
    {
        Flash::setOk($message);

        return $this;
    }

    /**
     * Message flash aves les erreurs.
     */
    final protected function withErrors(string $message): self
    {
        Flash::setError($message);

        return $this;
    }

    /**
     * Spécifier l'en-tête HTTP de l'affichage d'une vue.
     */
    final protected function header(string $content, string $type = null): self
    {
        Response::header($content, $type);

        return $this;
    }

    /**
     * Redirections.
     */
    final protected function redirect(string $path, int $httpResponseCodeParam = null): mixed
    {
        return Response::redirect($path, $httpResponseCodeParam);
    }
}
