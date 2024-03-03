<?php

declare(strict_types=1);

namespace DamianPhp\Support\Security;

use DamianPhp\Date\Date;
use DamianPhp\Support\Facades\Router;
use DamianPhp\Support\Facades\Server;
use App\Models\Attempt as AttemptModel;

/**
 * Limiter un X nombre de tentatives (de soumission de form par exemple...) WHERE une adresse IP.
 * Utile pour lutter contre les ataques brute force.
 * Généralement on va utiliser cette classe uniquement les autentifications.
 *
 * L'objectif :
 * - A chaque foit qu'un user WHERE condition (ip ou username par exemple) tente une autentification :
 *   Si user a dépassé le nombre de tentatives autorisé, on le bloque de ce formulaire.
 * - A chaque foit qu'un user WHERE condition (ip ou username par exemple) échoue sa tentative d'autentification :
 *   On incrémente 'number_attempts'.
 *
 * A chaque instance :
 * - [OPTIONAL] On précise le nombre de tentatives (d'échecs) autorisée;
 * - [OPTIONAL] On précise la durrée (en minuttes) qu'on veut bloquer l'user pour si il dépasse le nombre de tentatives autorisé;
 *
 * PS :
 * Un user bloqué, sera bloqué de uniquement des formuilaires en fonction du WHERE 'auth';
 * Si on met 'ip' pour 'field', on mettera le même 'auth' (histoire de bloquer l'IP de tout les formulaures 'auth').
 * Si on met 'username' pour 'field', on ne mettera pas le même 'auth' (histoire de bloquer un username uniquement sur un seul espace membres).
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Attempt
{
    /**
     * @var string - DB Driver to use.
     */
    private string $dbDriver;

    /**
     * @var string - Pour ne pas avoir de "conflits" entre plusieurs espaces membres.
     */
    private string $auth;

    /**
     * @var string - Pour condition SQL (si ou souhaite sécuriser WHERE IP : sera souvent une IP. si ou souhaite sécuriser WHERE user : sera un username ou un email).
     */
    private string $field;

    /**
     * @var null|AttemptModel - PS : sera null si il n'y a pas encore de ligne dans la BDD.
     */
    private ?AttemptModel $visitor;

    /**
     * @var int - Limitation autorisée de tentatives (nombre max d'échecs autorisés) par 24H.
     */
    private int $limitMaxAttempt = 40;

    /**
     * Durrée (en minutes) du blockage.
     *
     * @var int - 720 = 12 heures
     */
    private int $durationBlocking = 720;

    /**
     * @param string $field - Peut par exemple être une adresse IP ou un Username.
     */
    public function __construct(string $dbDriver, string $auth, string $field)
    {
        $this->dbDriver = $dbDriver;

        $this->auth = $auth;

        $this->field = $field;

        $this->visitor = AttemptModel::load()
            ->where('auth', '=', $this->auth)
            ->where('field', '=', $this->field)
            ->where('ip', '=', Server::getIp()) // utile pour si un pirate bloque un username (WHERE son IP), ne pas aussi bloquer le vrai user qui a cet username
            ->find();
    }

    /**
     * Setter de limitation autorisée de tentatives (nombre max d'échecs autorisé) par 24H.
     */
    public function setLimitAttempt(int $limitMaxAttempt): self
    {
        $this->limitMaxAttempt = $limitMaxAttempt;

        return $this;
    }

    /**
     * Setter de durrée (en minutes) du blockage.
     * (Il ne faut pas mettre + de 1440)
     */
    public function setDurationBlocking(int $durationBlocking): self
    {
        $this->durationBlocking = $durationBlocking;

        return $this;
    }

    /**
     * Vérifier si visiteur est autorisé de tenter une autentification.
     *
     * - Si le visiteur existe en BDD WHERE condition (ip ou username par exemple) :
     *   Si l'interval des dates dépasse en minutes la durationBlocking donnée en minutes, on le DELETE WHERE ce 'date_blocking'.
     *   Si 'number_attempts' est >= à limite de tentatives, on UPDATE visiteur pour ajouter 'date_blocking' et on return false.
     *
     * - Si le visiteur n'existe pas en BDD WHERE condition (ip ou username par exemple) : on ne le bloque pas.
     */
    public function isAuthorized(): bool
    {
        // Si le visiteur existe en BDD WHERE condition.
        if ($this->visitor !== null) {
            // Si l'interval des dates (date actuelle - date de bloquage) dépasse en minutes la durationBlocking donnée en minutes :
            // On le DELETE WHERE sa 'date_blocking', et on return true (pour débloquer le visiteur)
            if ($this->getMinWhoHaveBeenBlocked() > $this->durationBlocking) {
                AttemptModel::load()->where('auth', '=', $this->auth)->where('date_blocking', '=', $this->visitor->date_blocking)->delete();

                $this->visitor = null;  // pour éviter "conflit" dans increment()

                return true;
            }

            // Si 'number_attempts' est >= à limite de tentatives :
            // On UPDATE visiteur pour ajouter 'date_blocking' et on return false (pour bloquer le visiteur).
            if ($this->visitor->number_attempts >= $this->limitMaxAttempt) {
                if ($this->visitor->date_blocking === null) {
                    AttemptModel::load()
                        ->where('id', '=', $this->visitor->id)
                        ->update(['date_blocking' => (new Date())->format('Y-m-d H:i:s')]);
                }

                return false;
            }
        }

        return true;
    }

    /**
     * Incrémenter.
     *
     * - Si le visiteur existe en BDD WHERE condition (ip ou username par exemple) :
     *   On UPDATE WHERE condition (ip ou username par exemple) pour incrémenter 'number_attempts' du visiteur.
     * - Si le visiteur n'existe pas en BDD WHERE condition (ip ou username par exemple) :
     *   On l'INSERT.
     */
    public function increment()
    {
        // Si le visiteur existe en BDD WHERE condition.
        if ($this->visitor) {
            // On UPDATE WHERE condition (ip ou username par exemple) pour incrémenter 'number_attempts' du visiteur.
            AttemptModel::load()
                ->where('id', '=', $this->visitor->id)
                ->update(['number_attempts' => ($this->visitor->number_attempts + 1)]);
        } else {
            // Si le visiteur n'existe pas en BDD WHERE condition (ip ou username par exemple) -> on l'INSERT.
            AttemptModel::load()
                ->create([
                    'auth' => $this->auth,
                    'field' => $this->field,
                    'ip' => Server::getIp(),
                    'date_first_auth_failure' => (new Date())->format('Y-m-d H:i:s'),
                    'number_attempts' => 1,
                ]);
        }

        switch ($this->dbDriver) {
            case 'mysql':
                // On DELETE aussi toute les lignes qui ont échoués leur 1er login depuis >= de 24H.
                AttemptModel::load()->where('TIMESTAMPDIFF(HOUR, date_first_auth_failure, NOW())', '>=', 24)->delete();

                // On DELETE aussi toute les lignes qui sont bloqués depuis >= de 24H.
                AttemptModel::load()->where('TIMESTAMPDIFF(HOUR, date_blocking, NOW())', '>=', 24)->delete();

                break;
            case 'pgsql':
                // On DELETE aussi toute les lignes qui ont échoués leur 1er login depuis >= de 24H.
                AttemptModel::load()->where('EXTRACT(EPOCH FROM (NOW() - date_first_auth_failure)) / 3600', '>=', 24)->delete();

                // On DELETE aussi toute les lignes qui sont bloqués depuis >= de 24H.
                AttemptModel::load()->where('EXTRACT(EPOCH FROM (NOW() - date_blocking)) / 3600', '>=', 24)->delete();

                break;
            default:
                exit(); // On sécurise.
        }
    }

    /**
     * Calucler le nombre de minutes que visiteur a été bloqué.
     */
    private function getMinWhoHaveBeenBlocked(): int
    {
        $date_blocking = $this->visitor->date_blocking ?? (new Date())->format('Y-m-d H:i:s');

        $dateStart = new Date($date_blocking);
        $dateEnd = new Date();

        $interval = $dateStart->diff($dateEnd);

        $minutes = $interval->days * 24 * 60;
        $minutes += $interval->h * 60;
        $minutes += $interval->i;

        return $minutes;
    }

    /**
     * @return - Action d'erreur pour bloquer le visiteur.
     */
    public function getError()
    {
        return Router::getAction('App\Http\Controllers\Error\ErrorController@attempt', ['durationBlocking' => $this->durationBlocking]);
    }
}
