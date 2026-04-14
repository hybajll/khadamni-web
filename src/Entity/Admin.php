<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends User
{
    public const BUSINESS_ROLE_SUPER_ADMIN = 'super_admin';
    public const BUSINESS_ROLE_GESTIONNAIRE = 'gestionnaire';
    public const BUSINESS_ROLE_MODERATEUR = 'moderateur';

    public function isSuperAdmin(): bool
    {
        return $this->getAdminRole() === self::BUSINESS_ROLE_SUPER_ADMIN;
    }
}
