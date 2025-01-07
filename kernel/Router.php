<?php

namespace Sthom\Kernel;

use Sthom\Kernel\Utils\Security;

/**
 * Class Router
 * Cette classe permet de gérer le routage des requêtes HTTP vers les contrôleurs correspondants.
 */
class Router
{
    /**
     * Méthode principale pour dispatcher une requête HTTP vers le contrôleur approprié.
     *
     * @return void
     * @throws \Exception Si aucune route correspondante n'est trouvée ou si une méthode HTTP invalide est utilisée.
     */
    final public static function dispatch(): void
    {
        // Étape 1 : Inclure le fichier de configuration des routes
        include './../routes.php';

        // Étape 2 : Récupérer le chemin de la requête
        $currentPath = $_SERVER['REQUEST_URI'];
        $parameters = [];
        if (str_contains($currentPath, '?')) {
            $currentPath = explode('?', $currentPath)[0];
        }

        $isRouteFound = false;

        // Étape 3 : Parcourir les routes définies
        foreach (ROUTES as $path => $route) {
            // Convertir la route définie en regex pour les paramètres dynamiques
            $pattern = preg_replace('#\{(\w+)\}#', '(\\w+)', $path);
            $pattern = "#^" . $pattern . "$#";

            // Vérifier si l'URL correspond au pattern
            if (preg_match($pattern, $currentPath, $matches)) {
                // Vérifier la méthode HTTP
                $httpMethods = (array) $route['HTTP_METHODS'];
                if (!in_array($_SERVER['REQUEST_METHOD'], $httpMethods)) {
                    throw new \Exception('Method not allowed');
                }

                // Récupérer les noms des paramètres dynamiques
                preg_match_all('#\{(\w+)\}#', $path, $paramNames);
                $paramNames = $paramNames[1];

                // Associer les valeurs capturées à leurs noms
                array_shift($matches); // Retirer la première correspondance (l'URL complète)
                $parameters = array_combine($paramNames, $matches);

                // Vérifier l'authentification si nécessaire
                if (isset($route['AUTH'])) {
                    if (!Security::isConnected()) {
                        throw new \Exception('Unauthorized');
                    } elseif (is_array($route['AUTH']) && !Security::hasRole($route['AUTH'])) {
                        throw new \Exception('Forbidden');
                    }
                }

                // Appeler le contrôleur et la méthode
                $controller = $_ENV['CONTROLLER_NAMESPACE'] . $route['CONTROLLER'];
                $method = $route['METHOD'];
                $controllerInstance = new $controller();
                $controllerInstance->$method(...array_values($parameters));

                $isRouteFound = true;
                break;
            }
        }

        // Si aucune route n'est trouvée, lever une exception
        if (!$isRouteFound) {
            throw new \Exception('No route found');
        }
    }
}
