<?php

namespace Sthom\App\Model;

use Sthom\Kernel\Utils\UserInterface;

class User implements UserInterface
{
    public const TABLE = "user";
    private ?int $id;
    private ?string $name;
    private ?string $email;
    private ?string $password;

    private ?string $roles;


    public function getId(): int
    {
        return $this->id;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function setRoles(array $roles): void
    {
        // dd(json_encode($roles));
        $this->roles = json_encode($roles, JSON_THROW_ON_ERROR);
    }

    public function addRole(string $role): void
    {
        $roles = $this->getRoles();  // Récupérer les rôles actuels
        if (!in_array($role, $roles)) {
            $roles[] = $role;  // Ajouter le rôle
            $this->setRoles($roles);  // Ré-encoder et sauvegarder
        }

    }

    public function getRoles(): array
    {
        return json_decode($this->roles, true) ?: ['ROLE_USER'];
    }
}
