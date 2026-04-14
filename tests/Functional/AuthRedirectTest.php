<?php

namespace App\Tests\Functional;

use App\Entity\Admin;
use App\Entity\Etudiant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AuthRedirectTest extends FunctionalTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->rebuildDatabaseSchema();
    }

    public function testLoginRedirectsAdminToAdminDashboard(): void
    {
        $this->createAdmin('admin@example.com', 'pass1234');

        $client = $this->client;
        $client->request('GET', '/login');

        $client->submitForm('Se connecter', [
            '_username' => 'admin@example.com',
            '_password' => 'pass1234',
        ]);

        self::assertResponseRedirects('/admin/dashboard');
    }

    public function testLoginRedirectsUserToUserHome(): void
    {
        $this->createEtudiant('user@example.com', 'pass1234');

        $client = $this->client;
        $client->request('GET', '/login');

        $client->submitForm('Se connecter', [
            '_username' => 'user@example.com',
            '_password' => 'pass1234',
        ]);

        self::assertResponseRedirects('/user/home');
    }

    public function testInactiveUserCannotLogin(): void
    {
        $this->createEtudiant('inactive@example.com', 'pass1234', false);

        $client = $this->client;
        $client->request('GET', '/login');

        $client->submitForm('Se connecter', [
            '_username' => 'inactive@example.com',
            '_password' => 'pass1234',
        ]);

        self::assertResponseRedirects('/login');
        $client->followRedirect();
        self::assertStringContainsString('Votre compte est désactivé', $client->getResponse()->getContent() ?? '');
    }

    private function createAdmin(string $email, string $plainPassword): void
    {
        $admin = new Admin();
        $admin->setEmail($email);
        $admin->setAdminRole(Admin::BUSINESS_ROLE_SUPER_ADMIN);
        $admin->setIsActive(true);
        $admin->setLocalDateTime(new \DateTimeImmutable());

        $this->persistUserWithPassword($admin, $plainPassword);
    }

    private function createEtudiant(string $email, string $plainPassword, bool $isActive = true): void
    {
        $user = new Etudiant();
        $user->setEmail($email);
        $user->setIsActive($isActive);
        $user->setLocalDateTime(new \DateTimeImmutable());

        $this->persistUserWithPassword($user, $plainPassword);
    }

    private function persistUserWithPassword(object $user, string $plainPassword): void
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

        $entityManager->persist($user);
        $entityManager->flush();
    }
}
