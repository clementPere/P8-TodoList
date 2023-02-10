<?php

namespace App\DataFixtures;

use App\Entity\Task;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TaskFixtures extends Fixture
{
    // ...
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 30; $i++) {
            $task = new Task;
            $task->setTitle("Task " . $i)
                ->setContent("Contenu de la task " . $i)
                ->setIsDone(rand(0, 1))
                ->setCreatedAt(new DateTime("now"));
            $manager->persist($task);
        }
        $manager->flush();
    }
}
