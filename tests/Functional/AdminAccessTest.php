<?php

namespace App\Tests\Functional;

use App\Entity\Etudiant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AdminAccessTest extends FunctionalTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->rebuildDatabaseSchema();
    }

    public function testNormalUserCannotAccessAdminDashboard(): void
    {
        $this->createEtudiant('user@example.com', 'pass1234');

        $client = $this->client;
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'user@example.com',
            '_password' => 'pass1234',
        ]);
        $client->followRedirect();

        $client->request('GET', '/admin/dashboard');
        self::assertResponseStatusCodeSame(403);
    }

    private function createEtudiant(string $email, string $plainPassword): void
    {
        $user = new Etudiant();
        $user->setEmail($email);
        $user->setIsActive(true);
        $user->setLocalDateTime(new \DateTimeImmutable());

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

        $entityManager->persist($user);
        $entityManager->flush();
    }
}
