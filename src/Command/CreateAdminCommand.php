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
    name: 'app:create-admin',
    description: 'Create an admin user (bootstrap the first admin).',
)]
final class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Admin email')
            ->addArgument('password', InputArgument::REQUIRED, 'Admin password')
            ->addArgument('role', InputArgument::OPTIONAL, 'Business role: super_admin|gestionnaire|moderateur', Admin::BUSINESS_ROLE_SUPER_ADMIN);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (string) $input->getArgument('email');
        $password = (string) $input->getArgument('password');
        $role = (string) $input->getArgument('role');

        if ($this->userRepository->findOneBy(['email' => $email])) {
            $output->writeln('Email already exists.');
            return Command::FAILURE;
        }

        if (!in_array($role, [
            Admin::BUSINESS_ROLE_SUPER_ADMIN,
            Admin::BUSINESS_ROLE_GESTIONNAIRE,
            Admin::BUSINESS_ROLE_MODERATEUR,
        ], true)) {
            $output->writeln('Invalid role. Use: super_admin | gestionnaire | moderateur');
            return Command::FAILURE;
        }

        $admin = new Admin();
        $admin
            ->setEmail($email)
            ->setAdminRole($role)
            ->setIsActive(true)
            ->setLocalDateTime(new \DateTimeImmutable())
            ->setPassword($this->passwordHasher->hashPassword($admin, $password));

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $output->writeln('Admin created: '.$email);

        return Command::SUCCESS;
    }
}

