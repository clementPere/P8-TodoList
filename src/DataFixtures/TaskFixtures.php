<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker;

class TaskFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 50; $i++) {
            $task = new Task;
            $task->setTitle("Tache " . $i)
                ->setContent($faker->text(300))
                ->setIsDone(rand(0, 1))
                ->setUser($manager->getRepository(User::class)->find(rand(1, 11)))
                ->setCreatedAt($faker->dateTime());
            $manager->persist($task);
        }
        $manager->flush();
    }
}
