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

    final public function getApiUser(int $userId): void
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $userRepo = new Repository(User::class);
                $user = $userRepo->getById($userId);

                if(!$user) {
                    throw new Exception("User inconnu");
                }
                $this->json([
                    "user" => $user->toArray()
                ]);
            } catch(Exception $e) {
                $this->json([
                    "error" => $e->getMessage()
                ]);
            }
        } else {
            $this->json([
                'success' => false,
                "error" => "La méthode HTTP doit être un GET",
            ]);
        }
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = json_decode(file_get_contents('php://input'), true);
                if(!empty($data['name']) && !empty(['email']) && !empty(['password'])) {
                    $user = new User();
                    $repo = new Repository(User::class);
                    $user->setName($data['name']);
                    $user->setEmail($data['email']);
                    $user->setRoles(['ROLE_USER']);
                    $hashPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                    $user->setPassword($hashPassword);
                    $repo->insert($user);
                    $this->json([
                        'success' => true,
                        'message' => 'Utilisateur crée avec succès.'
                    ]);
                } else {
                    throw new \Exception("Tous les champs doivent obligatoirement être renseignés");
                }
            } catch(\Exception $e) {
                $this->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
            }
        } else {
            $this->json([
                'success' => false,
                'error' => 'La méthode HTTP doit être un POST',
            ]);
        }
    }

    final public function editApiUser(int $userId): void
    {
        if($_SERVER['REQUEST_METHOD'] === 'PATCH') {
            try {
                $repo = new Repository(User::class);
                $user = $repo->getById($userId);
                if(!$user) {
                    $this->json([
                       'error' => 'User inconnu',
                    ]);
                }
                $data = json_decode(file_get_contents('php://input'), true);
                $user->setName($data['name'] ?? $user->getName());
                $user->setEmail($data['email'] ?? $user->getEmail());
                $repo->update($user);
                $this->json([
                    'success' => true,
                ]);
            } catch (\Exception $e) {
                $this->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
            }
        } else {
            $this->json([
                'success' => false,
                'error' => 'La méthode HTTP doit être un PATCH',
            ]);
        }
    }

    // Le paramètres de $_GET peuvent etre récupérés via les paramètres de la méthode.
    // Attention il faudra prochainement utiliser la méthode http DELETE lorsque le mini-framework le permettra
}
