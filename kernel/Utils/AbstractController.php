<?php

namespace Sthom\Kernel\Utils;

/**
 * Class AbstractController
 * Cette classe définit des méthodes communes à tous les contrôleurs.
 * Elle fournit des fonctionnalités pour :
 * - Envoyer des réponses JSON
 * - Rendre des vues HTML
 * - Effectuer des redirections HTTP
 *
 * @package Sthom\Kernel\Utils
 */
class AbstractController
{

    /**
     * Méthode pour envoyer une réponse JSON au client.
     *
     * Cette méthode encode un tableau PHP en JSON et l'envoie au client
     * avec un code de statut HTTP. Elle est utile pour les APIs REST.
     *
     * @param array $data Les données à encoder en JSON.
     * @param int $status (optionnel) Le code de statut HTTP à envoyer (par défaut 200).
     * @return void
     *
     * Exemple d'utilisation :
     * ```php
     * $controller = new AbstractController();
     * $controller->json(['message' => 'Success'], 200);
     * ```
     * Résultat : Une réponse HTTP contenant `{"message":"Success"}` avec un statut HTTP 200.
     */
    public final function json(array $data, int $status = 200): void
    {
        try {
            // Nettoie et prépare les données
            $sanitizedData = $this->prepareData($data, true);
            // Définit les en-têtes HTTP
            $this->setHeaders($status, true);
            // Options d'encodage JSON
            $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
            // Encode les données en JSON
            $jsonResponse = json_encode($sanitizedData, $jsonOptions);
            // Vérifie les erreurs d'encodage
            if ($jsonResponse === false) {
                throw new \RuntimeException('Impossible d\'encoder les données JSON');
            }
            // Envoie la réponse
            echo $jsonResponse;
            // Termine le script
            exit;

        } catch (\Throwable $e) {
            throw new \RuntimeException('Erreur lors de l\'envoi de la réponse JSON', 500, $e);
        }
    }

    /**
     * Nettoie et prépare les données pour l'encodage JSON.
     *
     * @param array $data Données à préparer
     * @return array Données nettoyées et préparées
     */
    private function prepareData(array $data, bool $isJson = false): array
    {
        if (!$isJson) {
            return array_map(function ($value) {
                if (is_string($value)) {
                    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
                return $value;
            }, $data);
        } else {
            return array_map(function ($value) use ($isJson) {
                if (is_string($value)) {
                    // Échappe les caractères spéciaux pour JSON
                    return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
                // Gère différents types de données
                if (is_numeric($value)) {
                    return $value;
                }
                if (is_bool($value)) {
                    return $value;
                }
                if (is_array($value)) {
                    return $this->prepareData($value, $isJson);
                }
                return null;
            }, $data);
        }
    }


    /**
     * Définit les en-têtes HTTP pour la réponse JSON.
     *
     * @param int $status Code de statut HTTP
     */
    private function setHeaders(int $status, bool $isJson = false): void
    {
        http_response_code($status);
        if ($isJson) {
            header('Content-Type: application/json; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
        } else {
            header('Content-Type: text/html; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
        }
    }



    /**
     * Méthode pour afficher une vue HTML en y injectant des données dynamiques.
     *
     * Cette méthode rend un document HTML en incluant une vue spécifique
     * tout en passant des variables à celle-ci. Elle est utilisée pour
     * les applications web basées sur des vues.
     *
     * @param string $path Le chemin du fichier de vue (sans extension `.php`).
     * @param array $data (optionnel) Les données dynamiques à injecter dans la vue.
     * @param int $status (optionnel) Le code de statut HTTP (par défaut 200).
     * @return void
     *
     * Exemple d'utilisation :
     * ```php
     * $controller = new AbstractController();
     * $controller->render('home', ['title' => 'Bienvenue'], 200);
     * ```
     * Cela inclura le fichier `Views/home.php` et injectera une variable `$title` avec la valeur `'Bienvenue'`.
     */
    public final function render(string $path, array $data = [], int $status = 200): void
    {
        try {
            $view = __DIR__ . "/../../src/Views/" . $path;
            if (!file_exists($view)) {
                throw new \InvalidArgumentException("Vue introuvable : {$view}");
            }
            // Extrait les données de la vue de manière sécurisée
            $viewData = $this->prepareData($data);
            extract($viewData);
            // Définit les en-têtes HTTP
            $this->setHeaders($status);
            // Inclut le fichier de base HTML (layout principal)
            include __DIR__ . "/../../src/Views/base.php";
        } catch (\Throwable $e) {
            throw new \RuntimeException('Erreur lors du rendu de la vue', 500, $e);
        }
    }


    /**
     * Méthode pour rediriger l'utilisateur vers une autre URL.
     *
     * Cette méthode utilise un en-tête HTTP pour effectuer une redirection.
     * Elle est utile pour rediriger les utilisateurs après un traitement,
     * comme une soumission de formulaire.
     *
     * @param string $route L'URL ou la route vers laquelle rediriger.
     * @return void
     *
     * Exemple d'utilisation :
     * ```php
     * $controller = new AbstractController();
     * $controller->redirect('/login');
     * ```
     * Cela redirigera l'utilisateur vers `/login`.
     */
    public final function redirect(string $route): void
    {
        // Définit l'en-tête HTTP pour rediriger l'utilisateur vers une autre URL.
        header('Location: ' . $route);
        exit();
    }
}
