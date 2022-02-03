<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\District;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class DistrictFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (Article::DISTRICTS as $key => $article) {
            $district = new District;
            $district->setName(Article::DISTRICTS[$key]);
            $this->addReference('district_' . $key, $district);
            $manager->persist($district);
        }

        $manager->flush();
    }
}
