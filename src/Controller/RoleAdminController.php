<?php

namespace Sthom\App\Controller;

use Sthom\Kernel\Utils\AbstractController;

class RoleAdminController extends AbstractController
{
    public final function index(): void
    {
        $this->render('roleAdmin.php');
    }
}
