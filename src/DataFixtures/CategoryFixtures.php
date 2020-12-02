<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    const CAT1 = 'category1';
    const CAT2 = 'category2';
    const CAT3 = 'category3';

    public function load(ObjectManager $manager)
    {
        $category1 = new Category();
        $category1->setName('Informatique');
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setName('Cuisine');
        $manager->persist($category2);

        $category3 = new Category();
        $category3->setName('Divers');
        $manager->persist($category3);

        $manager->flush();

        $this->addReference(self::CAT1, $category1);
        $this->addReference(self::CAT2, $category2);
        $this->addReference(self::CAT3, $category3);
    }
}
