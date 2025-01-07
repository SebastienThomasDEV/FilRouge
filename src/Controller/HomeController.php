<?php

namespace Sthom\App\Controller;

use Sthom\Kernel\Utils\AbstractController;
use Sthom\Kernel\Utils\Security;

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

    final public function create(): void
    {
        $this->json(['message' => 'create']);
    }


}

