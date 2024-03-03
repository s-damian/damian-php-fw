<?php

declare(strict_types=1);

namespace DamianPhp\Http\Response;

/**
 * Curl.
 */
class Curl
{
    public static function get(string $url, array $options = []): array
    {
        // initialisez la session cURL
        $ch = curl_init();

        // options de transmission cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        // en-têtes possibles
        if (isset($options['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
        }

        return self::getResponse($ch);
    }

    public static function post(string $url, array $dataPost, array $options = []): array
    {
        // initialisez la session cURL
        $ch = curl_init();

        // options de transmission cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        // options pour POST
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);

        // en-têtes possibles
        if (isset($options['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
        }

        return self::getResponse($ch);
    }

    private static function getResponse($ch): array
    {
        // exécutez la session cURL
        $apiResponse = curl_exec($ch);

        // récupérer des informations (code d'état, etc.)
        $infos = curl_getinfo($ch);

        // fermez la session cURL pour libérer de la mémoire
        curl_close($ch);

        if ($apiResponse !== false) {
            $dataToReturn = json_decode($apiResponse);
        } else {
            $dataToReturn = ['error' => 'Une erreur est survenue.'];
        }

        return [
            'response_json' => $dataToReturn,
            'response_infos' => $infos,
        ];
    }
}
