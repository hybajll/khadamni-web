<?php

namespace App\Tests\Functional;

use App\Entity\Cv;
use App\Entity\Etudiant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CvImproveTest extends FunctionalTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->rebuildDatabaseSchema();
    }

    public function testImproveCreatesImprovedContent(): void
    {
        $user = $this->createEtudiantWithCv('user@example.com', 'pass1234');
        $userId = (int) $user->getId();

        $client = $this->client;
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'user@example.com',
            '_password' => 'pass1234',
        ]);
        $client->followRedirect();

        $client->request('GET', '/cv/');
        $content = $client->getResponse()->getContent() ?? '';
        self::assertStringContainsString('name="_token"', $content);

        preg_match('/name=\"_token\" value=\"([^\"]+)\"/', $content, $matches);
        $token = $matches[1] ?? '';
        self::assertNotSame('', $token);

        $client->request('POST', '/cv/ameliorer', [
            '_token' => $token,
        ]);
        self::assertResponseRedirects('/cv/view-improved');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $entityManager->clear();

        $reloadedUser = $entityManager->getRepository(Etudiant::class)->find($userId);
        self::assertNotNull($reloadedUser);

        $cv = $entityManager->getRepository(Cv::class)->findOneBy(['user' => $reloadedUser]);
        self::assertNotNull($cv);
        self::assertNotEmpty($cv->getContenuAmeliore());
    }

    private function createEtudiantWithCv(string $email, string $plainPassword): Etudiant
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

        $cv = new Cv();
        $cv->setUser($user);
        $cv->setTitre('Mon CV');
        $cv->setContenuOriginal("Développeur junior\nSymfony\nStage 2025");
        $cv->setDateUpload(new \DateTime());
        $cv->setNombreAmeliorations(0);
        $cv->setEstPublic(false);

        $entityManager->persist($user);
        $entityManager->persist($cv);
        $entityManager->flush();

        return $user;
    }
}
