<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\District;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    const ARTICLESFIXTURES = 10;

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < self::ARTICLESFIXTURES; $i++) {
            $article = new Article;
        $article->setTitle("La super visite à faire numéro" . $i);
        $article->setDescription(
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
            exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
        );
        $article->setDistrict($this->getReference('district_' . $i));
        $article->setDate((new DateTime()));
        $article->setPicture('example-image.png');
        $manager->persist($article);
        }
        $manager->flush();
    }


    public function getDependencies()
    {
        return [
            DistrictFixtures::class
        ];
    }
}
