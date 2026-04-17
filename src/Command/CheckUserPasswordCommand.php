<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:check-password',
    description: 'Check if a plain password matches the stored user password.',
)]
final class CheckUserPasswordCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'Plain password to verify');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (string) $input->getArgument('email');
        $plainPassword = (string) $input->getArgument('password');

        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user instanceof User) {
            $output->writeln('<error>User not found.</error>');
            return Command::FAILURE;
        }

        $isValid = $this->passwordHasher->isPasswordValid($user, $plainPassword);
        $output->writeln($isValid ? '<info>VALID</info>' : '<error>INVALID</error>');

        return $isValid ? Command::SUCCESS : Command::FAILURE;
    }
}

