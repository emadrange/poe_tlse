<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AuthorFixtures extends Fixture implements DependentFixtureInterface
{
    const AUTHOR_1 = 'author1';
    const AUTHOR_2 = 'author2';

    public function load(ObjectManager $manager)
    {
        $author1 = new Author();
        $author1->setPseudo('Rico')
            ->setUser($this->getReference(UserFixtures::USER_1));
        $manager->persist($author1);

        $author2 = new Author();
        $author2->setPseudo('Roro77')
            ->setUser($this->getReference(UserFixtures::USER_2));
        $manager->persist($author2);

        $manager->flush();

        $this->addReference(self::AUTHOR_1, $author1);
        $this->addReference(self::AUTHOR_2, $author2);
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
