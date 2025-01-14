<?php

namespace Sthom\App\Repository;

use Sthom\App\Model\Client;
use Sthom\Kernel\Database\AbstractRepository;

class ClientRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(Client::class);
    }

    public function findClientByRole()
    {
        return $this->customQuery("SELECT * FROM client WHERE role = 'ROLE_CLIENT'");
    }

    public function findClientByRoleAndName()
    {
        return $this->customQuery("SELECT * FROM client WHERE role = 'ROLE_CLIENT' AND name = :name", ['name' => 'Sthom']);
    }

}