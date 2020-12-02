<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $article1 = new Article();
        $article1->setTitle('Fixture 1')
            ->setSummary('Ceci est le résumé')
            ->setContent('Ceci est le contenu')
            ->setPublished(true)
            ->setCategory($this->getReference(CategoryFixtures::CAT1))
            ->setAuthor($this->getReference(AuthorFixtures::AUTHOR_1));
        $manager->persist($article1);

        $article2 = new Article();
        $article2->setTitle('Fixture 2')
            ->setSummary('Résumé de la fixture 2')
            ->setContent('Contenu de la fixture 2')
            ->setPublished(false)
            ->setCategory($this->getReference(CategoryFixtures::CAT2))
            ->setAuthor($this->getReference(AuthorFixtures::AUTHOR_2));
        $manager->persist($article2);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
            AuthorFixtures::class,
        ];
    }
}
