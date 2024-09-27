<?php

namespace App\Service;

use App\Entity\Categories;
use App\Repository\CategoriesRepository;

class CategoryService
{
    private $CategoriesRepository;

    public function __construct(CategoriesRepository $CategoriesRepository)
    {
        $this->CategoriesRepository = $CategoriesRepository;
    }

    public function GetCategoryById(int $Id): ?Categories
    {
        return $this->CategoriesRepository->find($Id);
    }
}