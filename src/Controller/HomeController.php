<?php

namespace Sthom\App\Controller;

use Sthom\App\Model\User;
use Sthom\App\Repository\ClientRepository;
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

    public final function show(int $id): void
    {
       $repo = new ClientRepository();
       $repo->findClientByRole();

    }

    final public function jsonExemple(): void
    {
        $this->json(['message' => 'create']);
    }



}