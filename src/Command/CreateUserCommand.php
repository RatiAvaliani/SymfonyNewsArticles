<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'CreateUserCommand',
    description: 'Create Admin Account',
)]
class CreateUserCommand extends Command
{
    private $PasswordHasher;
    private $EntityManager;

    public function __construct(UserPasswordHasherInterface $PasswordHasher, EntityManagerInterface $EntityManager)
    {
        $this->EntityManager = $EntityManager;
        $this->PasswordHasher = $PasswordHasher;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'Enter New Email')
            ->addArgument('password', InputArgument::OPTIONAL, 'Enter New Password')
            ->addArgument('roles', InputArgument::OPTIONAL, 'Enter User Roles')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $Io = new SymfonyStyle($input, $output);
        $Email = $input->getArgument('email');
        $Password = $input->getArgument('password');
        $Roles = $input->getArgument('roles');

        if ($Email && $Password) {
            $User = new User();
            $User->setEmail($Email);
            $User->setPassword($this->PasswordHasher->hashPassword(
                $User,
                $Password
            ));
            $User->setRoles([$Roles]);

            $this->EntityManager->persist($User);
            $this->EntityManager->flush();

            $Io->success('New User Added Successfully.');

            return Command::SUCCESS;
        }

        $Io->note('Email Or Password Is Missing.');

        return Command::FAILURE;
    }
}
