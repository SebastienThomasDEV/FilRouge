<?php

namespace Sthom\Kernel\Http;

use Sthom\Kernel\Security\Security;

class Router
{
    public final static function dispatch(): void
    {
        include './../routes.php';
        $currentPath = $_SERVER['REQUEST_URI'];
        $requestPath = explode('?', $currentPath)[0];

        $isRouteFound = false;

        foreach (ROUTES as $routePath => $route) {
            // Convertir les paramètres de route en pattern regex
            $pattern = self::convertRouteToRegex($routePath);

            // Vérifier si l'URL courante correspond au pattern
            if (preg_match($pattern, $requestPath, $matches)) {
                // Vérifier la méthode HTTP
                self::validateHttpMethod($route);

                // Vérifier l'authentification si nécessaire
                self::validateAuthentication($route);

                // Extraire les paramètres de l'URL
                $parameters = self::extractParameters($routePath, $requestPath);

                // Instancier le contrôleur et appeler la méthode
                $controller = $_ENV['CONTROLLER_NAMESPACE'] . $route['CONTROLLER'];
                $method = $route['METHOD'];

                try {
                    if (!class_exists($controller)) {
                        throw new \Exception("Controller '$controller' not found");
                    }

                    $controllerInstance = new $controller();
                    if (!method_exists($controllerInstance, $method)) {
                        throw new \Exception("Method '$method' not found in controller");
                    }

                    $controllerInstance->$method(...array_values($parameters));
                    $isRouteFound = true;
                    break;

                } catch (\Exception $e) {
                    throw new \Exception('Controller error: ' . $e->getMessage());
                }
            }
        }

        if (!$isRouteFound) {
            throw new \Exception('No route found for path: ' . $requestPath);
        }
    }

    /**
     * Convertit une route avec paramètres en expression régulière
     * Exemple: '/users/{id}' devient '#^/users/([^/]+)$#'
     */
    private static function convertRouteToRegex(string $route): string
    {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
        return '#^' . $pattern . '$#';
    }

    /**
     * Extrait les paramètres d'une URL en les comparant avec le pattern de la route
     */
    private static function extractParameters(string $routePath, string $requestPath): array
    {
        $parameters = [];
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));

        foreach ($routeParts as $index => $routePart) {
            if (preg_match('/\{([^}]+)\}/', $routePart, $matches)) {
                // Le paramètre est nommé selon ce qui est entre les accolades
                $paramName = $matches[1];
                $parameters[$paramName] = $requestParts[$index] ?? null;
            }
        }

        return $parameters;
    }

    /**
     * Valide la méthode HTTP utilisée
     */
    private static function validateHttpMethod(array $route): void
    {
        $allowedMethods = $route['HTTP_METHODS'];
        $currentMethod = $_SERVER['REQUEST_METHOD'];

        if (is_string($allowedMethods) && $currentMethod !== $allowedMethods) {
            throw new \Exception("Method not allowed: $currentMethod");
        }

        if (is_array($allowedMethods) && !in_array($currentMethod, $allowedMethods)) {
            throw new \Exception("Method not allowed: $currentMethod");
        }
    }

    /**
     * Valide l'authentification et les rôles si nécessaire
     */
    private static function validateAuthentication(array $route): void
    {
        if (isset($route['AUTH'])) {
            if (!Security::isConnected()) {
                throw new \Exception('Unauthorized: Authentication required');
            }

            if (is_array($route['AUTH']) && !Security::hasRole($route['AUTH'])) {
                throw new \Exception('Forbidden: Insufficient permissions');
            }
        }
    }
}