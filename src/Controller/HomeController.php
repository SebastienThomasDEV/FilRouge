<?php

namespace Sthom\App\Controller;

use Sthom\App\Model\User;
use Sthom\App\Repository\UserRepository;
use Sthom\Kernel\Http\AbstractController;
use Sthom\Kernel\Security\Security;

class HomeController extends AbstractController
{
    final public function index(): void
    {
        if (Security::isConnected()) {
            $user = $_SESSION['USER'];
        }
        $this->render('home/index.php', [
            'user' => $user,
        ]);
    }

    public final function create(int $id): void
    {
        $repo = new UserRepository();
        $user = $repo->find($id);
        $user = new User();
        $user->setName("tactac");
        $user->setRoles(["ROLE_USER"]);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setEmail('tactac@mail.com');
        $user->setPassword('123');
        $repo->insert($user);
        dd($user);
    }

    final public function jsonExemple(): void
    {
        $this->json(['message' => 'create']);
    }



}