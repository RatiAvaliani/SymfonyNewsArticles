<?php

namespace App\Controller;

use App\Repository\ArticlesRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function Index(CategoriesRepository $CategoriesRepository): Response
    {
        $Categories = $CategoriesRepository->findAll();

        $ArticlesQuery =
            $CategoriesRepository->createQueryBuilder('articles')
                ->select('a')
                ->from('App\Entity\Articles', 'a')
                ->innerJoin('a.categories', 'c')
                ->where('c.id IN (:categoryId)')
                ->setMaxResults(3);

        return $this->render('main/index.html.twig', [
            'Categories' => $Categories,
            'ArticlesQuery' => $ArticlesQuery
        ]);
    }
}
