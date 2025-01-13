<?php

namespace Sthom\App\Controller;

use Sthom\App\Model\User;
use Sthom\App\Repository\UserRepository;
use Sthom\Kernel\Http\AbstractController;

class HomeController extends AbstractController
{

    public final function index(int $id): void
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


}