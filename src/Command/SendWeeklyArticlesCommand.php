<?php

namespace App\Command;

use App\Entity\Articles;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment as TwigEnvironment;

#[AsCommand(
    name: 'SendWeeklyArticlesCommand'
)]
class SendWeeklyArticlesCommand extends Command
{
    private $EntityManager;
    private $Mailer;
    private $Twig;

    private $Env;

    public function __construct(EntityManagerInterface $EntityManager, MailerInterface $Mailer, TwigEnvironment $Twig)
    {
        $this->EntityManager = $EntityManager;
        $this->Mailer = $Mailer;
        $this->Twig = $Twig;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $Articles = $this->EntityManager->getRepository(Articles::class)
            ->createQueryBuilder('a')
            ->setMaxResults(10)
            ->orderBy('a.insert_date')
            ->getQuery()
            ->getResult();

        // Fetch all users from the database
        $Users = $this->EntityManager->getRepository(User::class)->findAll();

        foreach ($Users as $User) {
            $Email = (new Email())
                ->from('no-reply@yourdomain.com')
                ->to($User->getEmail())
                ->subject('Weekly Articles')
                ->html(
                    $this->Twig->render('email/weeklyArticles.html.twig', [
                        'user' => $User,
                        'articles' => $Articles,
                        'uri' => getenv("APP_URI")
                    ])
                );

            $this->Mailer->send($Email);
        }

        $output->writeln('Weekly articles sent to users.');

        return Command::SUCCESS;
    }
}
