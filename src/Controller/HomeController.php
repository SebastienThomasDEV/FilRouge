<?php

namespace Sthom\App\Controller;

use Sthom\Kernel\Utils\AbstractController;

class HomeController extends AbstractController
{
    public final function index(): void
    {
        $this->render('home/index.php');
    }

    public final function create(): void
    {
        $this->json(['message' => 'create']);
    }


}