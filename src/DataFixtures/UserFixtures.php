<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    const USER_1 = 'user1';
    const USER_2 = 'user2';

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setEmail('emadrange@dawan.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setFirstname('Eric')
            ->setLastname('Madrange')
            ->setPassword($this->encoder->encodePassword($user1, 'password'));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('rdurant@gmail.com')
            ->setRoles(['ROLE_USER'])
            ->setFirstname('Robert')
            ->setLastname('Durant')
            ->setPassword($this->encoder->encodePassword($user2, '1234'));
        $manager->persist($user2);

        $manager->flush();

        $this->addReference(self::USER_1, $user1);
        $this->addReference(self::USER_2, $user2);
    }
}
