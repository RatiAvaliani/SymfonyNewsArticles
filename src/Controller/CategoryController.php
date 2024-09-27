<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesFormType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{
    public function Index(CategoriesRepository $CategoriesRepository): Response
    {
        $Categories = $CategoriesRepository->findAll();
        return $this->render('admin/categories/index.html.twig', [
            'Categories' => $Categories
        ]);
    }

    public function Edit(Request $Request, Categories $Category, CategoriesRepository $CategoriesRepository, EntityManagerInterface $EntityManager): Response
    {
        $Category = $CategoriesRepository->find($Category->getId());

        $Form = $this->createForm(CategoriesFormType::class, $Category);
        $Form->handleRequest($Request);

        if ($Form->isSubmitted() && $Form->isValid()) {
            $EntityManager->persist($Category);
            $EntityManager->flush();

            $this->addFlash('Messages', 'Category Updated Successfully');

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/categories/edit.html.twig', [
            'Form' => $Form
        ]);
    }

    public function Add(Request $Request, EntityManagerInterface $EntityManager): Response
    {
        $Category = new Categories();

        $Form = $this->createForm(CategoriesFormType::class, $Category);
        $Form->handleRequest($Request);

        if ($Form->isSubmitted() && $Form->isValid()) {
            $EntityManager->persist($Category);
            $EntityManager->flush();

            $this->addFlash('Messages', 'Category added successfully');

            return $this->redirectToRoute('admin_categories');
        }


        return $this->render('admin/categories/add.html.twig', [
            'Form' => $Form->createView()
        ]);
    }

    public function Remove (Categories $Category, EntityManagerInterface $EntityManager) : Response
    {
        $EntityManager->remove($Category);
        $EntityManager->flush();

        $this->addFlash('Messages', 'Category removed successfully');

        return $this->redirectToRoute('admin_categories');
    }
}
