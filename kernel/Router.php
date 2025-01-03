<?php

namespace Sthom\Kernel;

use Sthom\Kernel\Utils\Security;

/**
 * Class Router
 * Cette classe permet de gérer le routage des requêtes HTTP vers les contrôleurs correspondants.
 * Elle compare l'URL demandée avec les routes définies dans un fichier de configuration (`routes.php`)
 * et appelle dynamiquement les méthodes des contrôleurs en fonction des correspondances.
 *
 * @package Sthom\Kernel
 */
class Router
{

    /**
     * Méthode principale pour dispatcher une requête HTTP vers le contrôleur approprié.
     *
     * Cette méthode :
     * - Compare l'URL de la requête aux routes définies.
     * - Vérifie que la méthode HTTP utilisée (GET, POST, etc.) est autorisée.
     * - Instancie dynamiquement le contrôleur associé et appelle la méthode correspondante.
     *
     * @return void
     * @throws \Exception Si aucune route correspondante n'est trouvée ou si une méthode HTTP invalide est utilisée.
     */
    public final static function dispatch(): void
    {
        // Étape 1 : Inclure le fichier de configuration des routes
        // Ce fichier doit définir une constante `ROUTES` contenant toutes les routes de l'application.
        include './../routes.php';

        /**
         * Étape 2 : Récupérer le chemin de la requête
         * - On utilise la superglobale `$_SERVER['REQUEST_URI']` pour obtenir l'URL demandée.
         * - Si l'URL contient des paramètres de requête (`?key=value`), ils sont extraits.
         */
        $currentPath = $_SERVER['REQUEST_URI'];
        $parameters = [];
        if (str_contains($currentPath, '?')) {
            // Séparer le chemin et les paramètres de requête
            $currentPath = explode('?', $currentPath)[0];
            $parameters = $_GET; // Récupérer les paramètres sous forme de tableau associatif
        }

        /**
         * Étape 3 : Initialiser un indicateur pour vérifier si une route correspondante est trouvée.
         * Si aucune route correspondante n'est trouvée, une exception sera levée à la fin.
         */
        $isRouteFound = false;

        /**
         * Étape 4 : Parcourir toutes les routes définies pour trouver une correspondance.
         * Chaque route est définie sous la forme `path => ['HTTP_METHODS' => ..., 'CONTROLLER' => ..., 'METHOD' => ...]`.
         */
        foreach (ROUTES as $path => $route) {
            /**
             * Étape 5 : Vérifier si la méthode HTTP est valide pour cette route.
             * - Les méthodes HTTP (GET, POST, etc.) peuvent être définies soit comme une chaîne, soit comme un tableau.
             * - On utilise `$_SERVER['REQUEST_METHOD']` pour récupérer la méthode HTTP utilisée par le client.
             */

            /**
             * Étape 6 : Vérifier si l'URL demandée correspond à la route actuelle.
             */
            if ($path === $currentPath) {
                /**
                 * Étape 7 : Construire dynamiquement le contrôleur et appeler la méthode correspondante.
                 * - Le contrôleur est défini par son namespace complet dans le fichier des routes.
                 * - La méthode à appeler est également spécifiée dans le fichier des routes.
                 */
                switch (gettype($route['HTTP_METHODS'])) {
                    case 'string':
                        // Si une seule méthode est autorisée, on vérifie qu'elle correspond à celle utilisée.
                        if ($_SERVER['REQUEST_METHOD'] !== $route['HTTP_METHODS']) {
                            throw new \Exception('Method not allowed'); // Erreur si la méthode est invalide
                        }
                        break;
                    case 'array':
                        // Si plusieurs méthodes sont autorisées, on vérifie qu'elles incluent celle utilisée.
                        if (!in_array($_SERVER['REQUEST_METHOD'], $route['HTTP_METHODS'])) {
                            throw new \Exception('Method not allowed'); // Erreur si aucune méthode ne correspond
                        }
                        break;
                    default:
                        throw new \Exception('Invalid HTTP_METHODS type'); // Erreur si le type est invalide
                }

                if (isset($route['AUTH'])) {
                    // Vérifier si l'utilisateur n'est PAS connecté
                    if (!Security::isConnected()) {
                        throw new \Exception('Unauthorized'); // L'utilisateur doit être connecté
                    }
                    // Si AUTH est un tableau, vérifier les rôles
                    elseif (is_array($route['AUTH']) && !Security::hasRole($route['AUTH'])) {
                        throw new \Exception('Forbidden'); // L'utilisateur n'a pas les rôles requis
                    }
                }
                $controller = $_ENV['CONTROLLER_NAMESPACE'] . $route['CONTROLLER'];
                $method = $route['METHOD'];

                /**
                 * Étape 8 : Instancier dynamiquement le contrôleur.
                 * - Exemple : Si `$controller = 'App\Controller\HomeController'`, alors `new $controller()` crée une instance de `HomeController`.
                 */
                $controllerInstance = new $controller();

                /**
                 * Étape 9 : Appeler dynamiquement la méthode du contrôleur avec les paramètres.
                 * - Les paramètres de la requête (GET) sont passés sous forme de tableau déplié grâce au "splat operator" (`...`).
                 * - Exemple : `index(1, 'John')` pour `/index.php?id=1&name=John`.
                 */
                $controllerInstance->$method(...array_values($parameters));

                // Marquer la route comme trouvée
                $isRouteFound = true;

                // Sortir de la boucle une fois la route correspondante trouvée
                break;
            }
        }

        /**
         * Étape 10 : Si aucune route correspondante n'a été trouvée, lever une exception.
         */
        if (!$isRouteFound) {
            throw new \Exception('No route found'); // Erreur si aucune route ne correspond
        }
    }
}
