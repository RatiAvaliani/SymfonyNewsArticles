<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Repository\ArticlesRepository;
use App\Repository\CommentsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CommentsController extends AbstractController
{
    public function __construct () {
    }

    public function Add (Request $Request, EntityManagerInterface $EntityManager, UserRepository $UserRepository, ArticlesRepository $ArticlesRepository): JsonResponse {
        $Content = json_decode($Request->getContent());
        $Article = $ArticlesRepository->find($Content->articleId);

        $User = $UserRepository->find($this->getUser()->getId());

        $Comments = new Comments();
        $Comments->setComment($Content->comment);
        $Comments->setArticleId($Article);
        $Comments->setUserId($User);

        $Comments->setInsertDate(new \DateTime('now'));
        $EntityManager->persist($Comments);

        $EntityManager->flush();

        return $this->Json(['Status' => true]);
    }

    public function Get (Request $Request, EntityManagerInterface $EntityManager, ArticlesRepository $Articles) :JsonResponse {
        $Content = json_decode($Request->getContent());

        $Query =
            $EntityManager->createQueryBuilder()
                ->select('c.comment', 'c.insertDate', 'u.email', 'c.id')
                ->from('App\Entity\Comments', 'c')
                ->innerJoin('App\Entity\User', 'u')
                ->where('c.article_id = :article')
                ->andWhere('u.id = c.user_id')
                ->setParameter('article', $Content->article)
                ->orderBy('c.insertDate', 'DESC');

        $Comments = $Query->getQuery()->getResult();

        return $this->Json(['Comments' => $Comments]);
    }

    public function Remove (EntityManagerInterface $EntityManager, Request $Request) :JsonResponse {
        $Content = json_decode($Request->getContent());

        $Comment = $EntityManager->getRepository(Comments::class)->find($Content->comment);

        $EntityManager->remove($Comment);
        $EntityManager->flush();

        return $this->Json(['Status' => true]);
    }

}
