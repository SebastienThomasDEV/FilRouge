<?php

namespace Sthom\App\Repository;

use Sthom\App\Model\User;
use Sthom\Kernel\Database\AbstractRepository;

class UserRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    public function findUserName()
    {
        return $this->customQuery("SELECT * FROM user WHERE name = :name", ['name' => 'Sthom']);
    }
}