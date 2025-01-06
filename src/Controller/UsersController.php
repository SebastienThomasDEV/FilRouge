<?php

namespace Sthom\App\Controller;

use Exception;
use Sthom\App\Model\User;
use Sthom\Kernel\Utils\AbstractController;
use Sthom\Kernel\Utils\Repository;

class UsersController extends AbstractController
{
    final public function index(): void
    {
        $userRepo = new Repository(User::class);
        $users = $userRepo->customQuery('SELECT * FROM user');

        $this->render('user/users.php', ["users" => $users]);
    }
    /**
     * Va chercher en base de données l'utilisateur dont l'id correspond à la valeur donnée en première valeur de la query string
     * ! C'est l'ordre des paramètres de la query string qui compte (peut importe la clé)
     * Sinon, on peut utiliser le $_GET pour utiliser les clés de la query string
     */
    final public function showUser(int $id): void
    {
        $userRepo = new Repository(User::class);
        $user = $userRepo->customQuery('SELECT * FROM user where user.id=:id', ["id" => $id]);

        $this->render('user/users.php', ["user" => $user]);
    }

    final public function getApiUsers(): void
    {
        $userRepo = new Repository(User::class);
        $users = $userRepo->customQuery('SELECT * FROM user');
        // Renvoie au format json
        $this->json($users);
    }

    final public function dynamicalUsers(): void
    {
        $userRepo = new Repository(User::class);
        $users = $userRepo->customQuery('SELECT * FROM user');
        $this->render('users/dynamicalUsers.php', ["users" => $users]);
    }

    final public function deleteApiUser(int $userId): void
    {
        // Récupération de l'id de l'élément à supprimer
        $userRepo = new Repository(User::class);
        if($userRepo->getById($userId)) {
            $userRepo->delete($userId);
            $this->json(["delete" => "true"]);
        }
        $this->json(["delete" => "false"]);
    }
    final public function addApiUser(): void
    {
        // On vérifie que la requête est bien de type POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Lire les données JSON envoyées dans le corps de la requête
                $data = json_decode(file_get_contents('php://input'), true);

                // On vérifie que les données ne sont pas vides
                if (!empty($data['name']) && !empty($data['email']) && !empty($data['password'])) {
                    // on crée l'instance User
                    $user = new User();
                    $repo = new Repository(User::class);
                    $user->setName($data['name']);
                    $user->setEmail($data['email']);
                    $user->setRoles(['ROLE_USER']); // Il faut passer un tableau ici
                    $hashPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                    $user->setPassword($hashPassword);
                    $repo->insert($user);

                    // Répondre avec un succès
                    echo json_encode([
                        'success' => true,
                        'message' => 'Utilisateur créé avec succès.'
                    ]);
                } else {
                    throw new \Exception("Tous les champs obligatoires doivent être renseignés.");
                }
            } catch(\Exception $e) {
                // En cas d'erreur, renvoyer un message d'erreur
                echo json_encode([
                    'success'   => false,
                    'error'     => $e->getMessage(),
                ]);
            }
        } else {
            // Si la méthode n'est pas POST
            echo json_encode([
                'success' => false,
                'error' => 'La méthode HTTP doit être POST'
            ]);
        }
    }
    // Le paramètres de $_GET peuvent etre récupérés via les paramètres de la méthode.
    // Attention il faudra prochainement utiliser la méthode http DELETE lorsque le mini-framework le permettra
}
