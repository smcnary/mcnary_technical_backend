<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a test user for API testing',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Check if user already exists
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);
        
        if ($existingUser) {
            $io->warning('User test@example.com already exists!');
            return Command::SUCCESS;
        }

        // Create new user
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setStatus('active');
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        
        // Hash password
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
        $user->setPasswordHash($hashedPassword);

        // Persist user
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Test user created successfully!');
        $io->info('Email: test@example.com');
        $io->info('Password: password123');

        return Command::SUCCESS;
    }
}
