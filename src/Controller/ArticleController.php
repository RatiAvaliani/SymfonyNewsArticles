<?php
namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Categories;
use App\Form\ArticlesFromType;
use App\Repository\ArticlesRepository;
use App\Service\ArticleService;
use App\Service\CategoryService;
use App\Service\ImageUploaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends AbstractController
{
    private $ArticleService;
    private $CategoryService;
    private $ImageUploaderService;

    public function __construct(
        ArticleService $ArticleService,
        CategoryService $CategoryService,
        ImageUploaderService $ImageUploaderService
    ) {
        $this->ArticleService = $ArticleService;
        $this->CategoryService = $CategoryService;
        $this->ImageUploaderService = $ImageUploaderService;
    }

    public function Index(ArticlesRepository $ArticlesRepository): Response
    {
        $Articles = $ArticlesRepository->findAll();

        return $this->render('admin/articles/index.html.twig', [
            'Articles' => $Articles
        ]);
    }

    public function ArticlesByCategory(
        Categories $Category,
        int $Page
    ): Response {
        $Category = $this->CategoryService->GetCategoryById($Category->getId());
        $CategoryTitle = $Category->getTitle();

        $Result = $this->ArticleService->GetArticlesByCategory($Category->getId(), $Page);

        return $this->render('main/articles/by_category.html.twig', [
            'CategoryTitle' => $CategoryTitle,
            'Paginator' => [
                'MaxPages' => $Result['MaxPages'],
                'CurrentPage' => $Result['CurrentPage'],
                'CategoryId' => $Category->getId(),
            ],
            'Articles' => $Result['Articles']
        ]);
    }

    public function Article(Articles $Article, ArticlesRepository $ArticlesRepository): Response
    {
        $Article = $ArticlesRepository->find($Article->getId());
        $Categories = $Article->getCategories()->toArray();

        return $this->render('main/articles/index.html.twig', [
            'Article' => $Article,
            'Categories' => $Categories
        ]);
    }

    public function Edit(
        Request $Request,
        Articles $Article,
        ArticlesRepository $ArticlesRepository
    ): Response {
        $Article = $ArticlesRepository->find($Article->getId());
        $Form = $this->createForm(ArticlesFromType::class, $Article);
        $Form->handleRequest($Request);

        if ($Form->isSubmitted() && $Form->isValid()) {
            if ($Image = $Request->files->get('articles_from')['image']) {
                $NewFileName = $this->ImageUploaderService->Upload($Image);
                $Article->setImage($NewFileName);
            }

            $this->ArticleService->SaveArticle($Article);
            $this->addFlash('Messages', 'Article edited successfully!');

            return $this->redirect($this->generateUrl('admin_articles'));
        }

        return $this->render('admin/articles/edit.html.twig', [
            'Form' => $Form->createView()
        ]);
    }

    public function Add(Request $Request): Response
    {
        $Article = new Articles();
        $Form = $this->createForm(ArticlesFromType::class, $Article);
        $Form->handleRequest($Request);

        if ($Form->isSubmitted() && $Form->isValid()) {
            if ($Image = $Request->files->get('articles_from')['image']) {
                $NewFileName = $this->ImageUploaderService->Upload($Image);
                $Article->setImage($NewFileName);
            }

            $this->ArticleService->SaveArticle($Article);
            $this->addFlash('Messages', 'Article added successfully!');

            return $this->redirect($this->generateUrl('admin_articles'));
        }

        return $this->render('admin/articles/add.html.twig', [
            'Form' => $Form->createView()
        ]);
    }

    public function Remove(Articles $Article): Response
    {
        $this->ArticleService->RemoveArticle($Article);
        $this->addFlash('Messages', 'Article removed successfully!');

        return $this->redirect($this->generateUrl('admin_articles'));
    }
}