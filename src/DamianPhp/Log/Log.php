<?php

namespace DamianPhp\Log;

use DamianPhp\Support\Helper;
use DamianPhp\Filesystem\File;
use DamianPhp\Support\Facades\Date;
use DamianPhp\Contracts\Log\LogInterface;

/**
 * Classe client.
 * Gestion des Logs.
 * Peut fonctionner avec une Facade.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Log implements LogInterface
{
    public function __construct()
    {
        if (! file_exists(Helper::storagePath('logs'))) {
            File::createDir(Helper::storagePath('logs'));
        }
    }

    /**
     * @param $error
     * @return string - type d'erreur PHP
     */
    public function typeErrorPhp($error): string
    {
        switch ($error) {
            case E_ERROR:
                return 'Error';
            case E_WARNING:
                return 'Warning';
            case E_PARSE:
                return 'Parse Error';
            case E_NOTICE:
                return 'Notice';
            case E_CORE_ERROR:
                return 'Core Error';
            case E_CORE_WARNING:
                return 'Core Warning';
            case E_COMPILE_ERROR:
                return 'Compile Error';
            case E_COMPILE_WARNING:
                return 'Compile Warning';
            case E_USER_ERROR:
                return 'User Error';
            case E_USER_WARNING:
                return 'User Warning';
            case E_USER_NOTICE:
                return 'User Notice';
            case E_STRICT:
                return 'Strict Notice';
            case E_RECOVERABLE_ERROR:
                return 'Recoverable Error';
            default:
                return 'Unknown error ('.$error.')';
        }
    }

    /**
     * Envoyer un log d'information de l'app (pour les fichiers qui sont dans le dossier "app").
     *
     * @param string $file - Pour éventuellement y logger dans un fichier spécifique.
     */
    public function infoApp(string $message, string $file = 'app-infos'): void
    {
        $logFilePath = Helper::storagePath('logs/'.$file.'.log');

        if (file_exists($logFilePath)) {
            $fp = fopen($logFilePath, 'a+');
            fseek($fp, SEEK_END);

            $messageFinal = '['.Date::getDateTimeFormat('d/m/Y H:i:s').']'."\r\n".'- URL: '.Helper::getActiveUrl().''."\r\n";
            $messageFinal .= '- Message: '.$message."\r\n"."\r\n";

            fwrite($fp, $messageFinal);
            fclose($fp);
        } else {
            File::createFile($logFilePath);
        }
    }

    /**
     * Envoyer un log d'erreur de l'app (pour les fichiers qui sont dans le dossier "app").
     *
     * @param string $file - Pour éventuellement y logger dans un fichier spécifique.
     */
    public function errorApp(string $message, string $file = 'app-errors'): void
    {
        $logFilePath = Helper::storagePath('logs/'.$file.'.log');

        if (file_exists($logFilePath)) {
            $filesLines = $this->debugFilesAndLines();

            $fp = fopen($logFilePath, 'a+');
            fseek($fp, SEEK_END);

            $messageFinal = '['.Date::getDateTimeFormat('d/m/Y H:i:s').']'."\r\n".'- URL: '.Helper::getActiveUrl().''."\r\n";
            $messageFinal .= '- Message: '.$message."\r\n".$filesLines."\r\n";

            fwrite($fp, $messageFinal);
            fclose($fp);
        } else {
            File::createFile($logFilePath);
        }
    }

    /**
     * Envoyer un log d'erreur du framework (pour les fichiers qui sont dans le dossier "core").
     */
    public function errorDamianPhp(string $message): void
    {
        $logFilePath = Helper::storagePath('logs/errors-damian-php.log');

        if (file_exists($logFilePath)) {
            $filesLines = $this->debugFilesAndLines();

            $fp = fopen($logFilePath, 'a+');
            fseek($fp, SEEK_END);

            $messageFinal = '['.Date::getDateTimeFormat('d/m/Y H:i:s').']'."\r\n".'- URL: '.Helper::getActiveUrl().''."\r\n";
            $messageFinal .= '- Message: '.$message."\r\n".$filesLines."\r\n";

            fwrite($fp, $messageFinal);
            fclose($fp);
        } else {
            File::createFile($logFilePath);
        }
    }

    /**
     * @return string - Les fichiers appelés et les lignes du log.
     */
    private function debugFilesAndLines(): string
    {
        $debug = debug_backtrace();

        $filesLines = '';
        foreach ($debug as $value) {
            foreach ($value as $k => $v) {
                if ($k === 'file') {
                    $filesLines .= $v;
                }
                if ($k === 'line') {
                    $filesLines .= '('.$v.')'."\r\n";
                }
            }
        }

        return $filesLines;
    }
}
