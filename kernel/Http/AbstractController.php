<?php

namespace Sthom\Kernel\Http;

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
        Response::json($data, $status);
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
        Response::html($path, $data, $status);
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
        Response::redirect($route);
    }
}
