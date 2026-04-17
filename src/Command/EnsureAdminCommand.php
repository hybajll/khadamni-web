<?php

namespace App\Command;

use App\Entity\Admin;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:ensure-admin',
    description: 'Create (or update) an admin user with a known password.',
)]
final class EnsureAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Admin email')
            ->addArgument('password', InputArgument::REQUIRED, 'Plain password')
            ->addArgument('role', InputArgument::OPTIONAL, 'Business role (super_admin|gestionnaire|moderateur)', Admin::BUSINESS_ROLE_SUPER_ADMIN);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (string) $input->getArgument('email');
        $plainPassword = (string) $input->getArgument('password');
        $businessRole = (string) $input->getArgument('role');

        $existing = $this->userRepository->findOneBy(['email' => $email]);

        if ($existing !== null && !$existing instanceof Admin) {
            $output->writeln(sprintf('<error>User "%s" exists but is not an Admin.</error>', $email));
            return Command::FAILURE;
        }

        $admin = $existing instanceof Admin ? $existing : new Admin();
        $admin
            ->setEmail($email)
            ->setPassword($this->passwordHasher->hashPassword($admin, $plainPassword))
            ->setIsActive(true)
            ->setAdminRole($businessRole)
            ->setLocalDateTime(new \DateTimeImmutable());

        if (!$existing) {
            $this->entityManager->persist($admin);
        }

        $this->entityManager->flush();

        $output->writeln('<info>Admin ready.</info>');
        $output->writeln(sprintf('Email: %s', $email));
        $output->writeln(sprintf('Password: %s', $plainPassword));
        $output->writeln(sprintf('Role: %s', $businessRole));

        return Command::SUCCESS;
    }
}

