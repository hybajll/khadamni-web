<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:hash-user-passwords',
    description: 'Hash plain passwords stored in the user table and migrate them to the Symfony password hasher.',
)]
class HashUserPasswordsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        if (!$users) {
            $output->writeln('<comment>Aucun utilisateur trouvé.</comment>');
            return Command::SUCCESS;
        }

        $updatedCount = 0;

        foreach ($users as $user) {
            if (!$user instanceof User) {
                continue;
            }

            $currentPassword = $user->getPassword();
            if (empty($currentPassword)) {
                continue;
            }

            $info = password_get_info($currentPassword);
            if ($info['algo'] === 0 || $info['algoName'] === 'unknown') {
                $newHash = $this->passwordHasher->hashPassword($user, $currentPassword);
                $user->setPassword($newHash);
                $updatedCount++;
            }
        }

        if ($updatedCount > 0) {
            $this->entityManager->flush();
        }

        $output->writeln(sprintf('<info>%d mot(s) de passe mis à jour.</info>', $updatedCount));

        return Command::SUCCESS;
    }
}
