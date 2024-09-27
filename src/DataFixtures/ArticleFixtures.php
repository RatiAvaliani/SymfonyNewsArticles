<?php

namespace App\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Articles;
use App\Entity\Categories;


class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $CategoryEntities = [];

        for ($b = 0; $b < 11; $b++) {
            $category = new Categories();
            $category->setTitle("Test Category " . $b);
            $CategoryEntities[] = $category;
            $manager->persist($category);
        }

        for ($i = 0; $i < 500; $i++) {
            $randomCategoryIds = rand(0, 9);

            $article = new Articles();
            $article->setTitle("Test Article " . $i);
            $article->setContent("Content " . $i);
            $article->setShortDescription("Short description " . $i);
            $article->setImage("e6f095e985e6762f1538b47ff1abda58dbe8ce088b16d8a5744fc179e9a2fc161727043214.png");
            $article->addCategory($CategoryEntities[$randomCategoryIds]);
            $article->setInsertDate(new \DateTime());
            $manager->persist($article);
        }

        $manager->flush();
    }
}


