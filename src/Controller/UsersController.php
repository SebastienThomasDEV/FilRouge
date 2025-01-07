<?php

namespace Sthom\App\Controller;

use Sthom\App\Model\User;
use Sthom\Kernel\Utils\AbstractController;
use Sthom\Kernel\Utils\Repository;

class UsersController extends AbstractController
{
    public final function index(): void
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
        // Récupération de l'utilisateur à ajouter
        dd($_POST);
    }
    // Le paramètres de $_GET peuvent etre récupérés via les paramètres de la méthode. 
    // Attention il faudra prochainement utiliser la méthode http DELETE lorsque le mini-framework le permettra
}
