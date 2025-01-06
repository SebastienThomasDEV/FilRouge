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

    final public function deleteApiUser(): void
    {
        // Récupération de l'id de l'élément à supprimer
        if (isset($_GET["id"])) {
            $userRepo = new Repository(User::class);
            $userRepo->delete($_GET["id"]);
            $this->json(["delete" => "true"]);
        } else {
            $this->json(["delete" => "false"]);
        }
    }
    final public function addApiUser(): void
    {
        // On vérifie que la requête est bien de type POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // on vérifie que les données ne sont pas vides
                if (!empty($_POST['name']) || !empty($_POST['email']) || !empty($_POST['password'])) {
                    // on crée l'instance User
                    $user = new User();
                    $repo = new Repository(User::class);
                    $user->setName($_POST['name']);
                    $user->setEmail($_POST['email']);
                    $user->setRoles('ROLE_USER');
                    $hashPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $user->setPassword($hashPassword);
                    $repo->insert($user);
                    $this->json([
                        'success' => true,
                        'message' => 'Utilisateur crée avec succès.'
                    ]);
                } else {
                    throw new \Exception("Tous les champs obligatoires doivent être renseignés.");
                }
            } catch(\Exception $e) {
                $this->json([
                    'success'   => false,
                    'error'     => $e->getMessage(),
                ]);
            }
        } else {
            $this->json([
                'success' => false,
                'error' => 'La méthode HTPP doit être POST'
            ]);
        }

    }
    // Le paramètres de $_GET peuvent etre récupérés via les paramètres de la méthode.
    // Attention il faudra prochainement utiliser la méthode http DELETE lorsque le mini-framework le permettra
}
