<?php

namespace Sthom\App\Controller;

use Sthom\Kernel\Utils\AbstractController;

class RoleUserController extends AbstractController
{
    public final function index(): void
    {
        $this->render('roleUser.php');
    }
}
