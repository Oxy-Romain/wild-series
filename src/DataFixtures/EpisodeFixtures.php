<?php

namespace App\DataFixtures;

use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Episode;
use Faker;
class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i=0 ; $i<300 ; $i++){
            $episode = new Episode();
            $slugify = new Slugify();
            $faker = Faker\Factory::create('us_US');
            $episode->setTitle($faker->sentence);
            $episode->setNumber($faker->randomDigit);
            $episode->setSynopsis($faker->paragraph);
            $slug = $slugify->generate($episode->getTitle());
            $episode->setSlug($slug);
            $episode->setSeason($this->getReference('season_' . rand(0 , 49)));
            $manager->persist($episode);
            $this->addReference('episode_' . $i, $episode);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }
}