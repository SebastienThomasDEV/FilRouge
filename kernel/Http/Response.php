<?php

namespace Sthom\Kernel\Http;
class Response
{
    /**
     * Envoie une réponse JSON.
     *
     * @param array $data Données à encoder en JSON
     * @param int $status Code de statut HTTP
     */
    public static function json(array $data, int $status = 200): void
    {
        self::sanitizeData($data);
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Envoie une réponse HTML.
     *
     * @param string $html HTML à envoyer
     * @param int $status Code de statut HTTP
     */
    public static function html(string $path, array $data, int $status = 200): void
    {
        $fullPath = __DIR__ . "/../../src/View/$path";
        if (file_exists($fullPath)) {
            http_response_code($status);
            header('Content-Type: text/html; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
            self::sanitizeData($data);
            $data['view'] = $fullPath;
            extract($data);
            include __DIR__ . "/../../src/View/base.php";
        } else {
            throw new \InvalidArgumentException("Vue introuvable : {$fullPath}");
        }
    }

    /**
     * Envoie une réponse de redirection.
     *
     * @param string $url URL de redirection
     * @param int $status Code de statut HTTP
     */
    public static function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header('Location: ' . $url, true, $status);
        exit;
    }


    private static function sanitizeData(array &$data): void
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            } elseif (is_int($value) || is_float($value)) {
                $data[$key] = (string)$value;
            } elseif (is_array($value)) {
                self::sanitizeData($data[$key]);
            } elseif (is_bool($value)) {
                $data[$key] = $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $data[$key] = 'null';
            } elseif (is_object($value)) {
                throw new \InvalidArgumentException('Impossible de sérialiser un objet');
            } elseif (is_resource($value)) {
                throw new \InvalidArgumentException('Impossible de sérialiser une ressource');
            } else {
                throw new \InvalidArgumentException('Type de données non géré');
            }
        }
    }



}