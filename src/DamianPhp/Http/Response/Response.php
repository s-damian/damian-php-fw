<?php

declare(strict_types=1);

namespace DamianPhp\Http\Response;

use DamianPhp\Support\Helper;
use DamianPhp\Contracts\Http\Response\ResponseInterface;

/**
 * Response.
 * Peut fonctionner avec une Facade.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Response implements ResponseInterface
{
    /**
     * Codes des réponses HTTP.
     */
    public const STATUS_CODE = [
        // Information 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        // Successful 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        210 => 'Content Different',
        226 => 'IM Used',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        310 => 'Too many Redirects',

        // Client error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        418 => 'I\'m a teapot',
        421 => 'Bad mapping / Misdirected Request',
        422 => 'Unprocessable entity',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        451 => 'Unavailable For Legal Reasons',
        456 => 'Unrecoverable Error',
        499 => 'Client has closed connection',

        // Server error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient storage',
        508 => 'Loop detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not extended',
        511 => 'Network authentication required',
        520 => 'Web server is returning an unknown error',
    ];

    /**
     * @param int - Code de la réponse HTTP.
     */
    public function getHttpResponseCode(): int|bool
    {
        return http_response_code();
    }

    /**
     * Spécifier l'en-tête HTTP de l'affichage d'une vue.
     */
    public function header(string $content, ?string $type = null): void
    {
        if ($type) {
            header($content.': '.$type.'; charset='.Helper::config('app')['charset']);
        } else {
            header($content);
        }
    }

    /**
     * Rediriger.
     */
    public function redirect(string $url, ?int $httpResponseCodeParam = null): never
    {
        if ($httpResponseCodeParam) {
            if (array_key_exists($httpResponseCodeParam, self::STATUS_CODE)) {
                $httpResponseCode = $httpResponseCodeParam;

                header('Location: '.$url, true, $httpResponseCode);
            } else {
                Helper::getExceptionOrLog('Status code "'.$httpResponseCodeParam.'" not good.');

                header('Location: '.$url);
            }
        } else {
            header('Location: '.$url);
        }

        exit();
    }

    /**
     * Retourner le contenu d'un fichier .php en string.
     *
     * @param string $path - Path du fichier.
     * @param array|null $data - Pour renvoyer les éventuelles données à la vue.
     * @return string - Contenu d'un fichier.
     */
    public function share(string $path, array $data = []): string
    {
        if ($data) {
            extract($data);
        }

        ob_start();
        require Helper::basePath($path.'.php');

        return ob_get_clean();
    }

    /**
     * Pour les messages de confirmation.
     */
    public function alertSuccess(string $message): string
    {
        return $this->setAlert('block-alert-success', $message);
    }

    /**
     * Pourles messages d'erreur.
     */
    public function alertError(string $message): string
    {
        return $this->setAlert('block-alert-error', $message);
    }

    /**
     * Pour les messages de confirmations ou d'erreurs dans le site.
     *
     * @param string $css - class CSS.
     * @param string $message - Message(s) d'info ou d'erreur.
     */
    public function setAlert(string $css, string $message): string
    {
        $html = '';

        $html .= '<span class="block-alert '.$css.'">';
        $html .=     '<span>'.$message.'</span>';
        $html .=     '<button type="button" class="close"></button>';
        $html .= '</span>';

        return $html;
    }
}
