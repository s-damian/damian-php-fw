<?php

declare(strict_types=1);

namespace DamianPhp\AppContracts\Http\Controllers\Error;

/**
 * Pour imposer convention(s) sur le controller App\Http\Controllers\Error\ErrorController
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface ErrorInterface
{
    /**
     * Show 404 error page (page not found).
     */
    public function error404();

    /**
     * Show 503 error page (website under maintenance).
     */
    public function error503();

    /**
     * Show 403 error page (forbiden - no right to access the website).
     */
    public function error403();

    /**
     * Show 403 attempt page (403 forbiden - not the right to access the site because number of connection attempts exceeded).
     *
     * @param int $durationBlocking - Duration of blocking WHERE IP.
     */
    public function attempt(int $durationBlocking);
}
