<?php

namespace App\DataFixtures;

use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        for($i = 0; $i < 10; $i++){
            $wish = new Wish();
            $wish->setTitle($faker->word());
            $wish->setAuthor($faker->name());
            $wish->setDateCreated(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));
            $wish->setPublished($faker->numberBetween(0, 1));
            $wish->setDescription($faker->realText());
            $manager->persist($wish);
        }

        $manager->flush();
    }
}
