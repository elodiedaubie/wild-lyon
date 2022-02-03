<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USERSFIXTURES = 3;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        /**
         * creation of simple users
         */
        for ($i = 0 ; $i < self::USERSFIXTURES; $i++) {
            $user = new User;
            $user->setEmail('user' . $i . '@monsite.com');
            $user->setPseudo('Tic' . $i);
            $user->setRoles(['ROLE_CONTRIBUTOR']);
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'Motdepasse!1'
            );
            $user->setPassword($hashedPassword);
            $this->addReference('user_' . $i, $user);
            $manager->persist($user);
        }

        /**
         * creation of one admin
         */
        $user = new User;
        $user->setEmail('admin@monsite.com');
        $user->setPseudo('Tac');
        $user->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'Motdepasse!1'
        );
        $user->setPassword($hashedPassword);
        $this->addReference('admin_1', $user);
        $manager->persist($user);

        $manager->flush();
    }
}
