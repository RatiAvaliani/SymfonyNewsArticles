<?php

namespace App\Service;

use App\Entity\Articles;
use App\Entity\Comments;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ArticleService
{
    private $EntityManager;

    public function __construct(EntityManagerInterface $EntityManager)
    {
        $this->EntityManager = $EntityManager;
    }

    public function GetArticlesByCategory(int $CategoryId, int $Page, int $MaxResults = 10)
    {
        $Query = $this->EntityManager->createQueryBuilder()
            ->select('a')
            ->from(Articles::class, 'a')
            ->innerJoin('a.categories', 'c')
            ->where('c.id = :CategoryId')
            ->setParameter('CategoryId', $CategoryId)
            ->setMaxResults($MaxResults);

        $Paginator = new Paginator($Query, true);
        $TotalItems = count($Paginator);
        $MaxPage = ceil($TotalItems / $MaxResults);
        $Offset = ($Page - 1) * $MaxResults;

        if ($Offset >= $TotalItems) {
            $Offset = ($MaxPage - 1) * $MaxResults;
        }

        $Paginator->getQuery()->setFirstResult($Offset);

        return [
            'Articles' => $Paginator,
            'MaxPages' => $MaxPage,
            'CurrentPage' => $Page,
        ];
    }

    public function SaveArticle(Articles $Article)
    {
        $this->EntityManager->persist($Article);
        $this->EntityManager->flush();
    }

    public function RemoveArticle(Articles $Article)
    {
        $Comments = $this->EntityManager->getRepository(Comments::class)->findBy(['article_id' => $Article->getId()]);

        foreach ($Comments as $Comment) {
            $this->EntityManager->remove($Comment);
            $this->EntityManager->flush();
        }

        $this->EntityManager->remove($Article);
        $this->EntityManager->flush();
    }
}