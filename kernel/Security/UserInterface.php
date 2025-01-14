<?php

namespace Sthom\Kernel\Security;

interface UserInterface
{
    public function setRoles(array $roles): void;
    public function getRoles(): array;

    public function getPassword(): string;
    public function setPassword(string $password): void;
}
