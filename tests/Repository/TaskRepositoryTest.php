<?php

namespace App\Tests\Repository;

use DateTime;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSave()
    {
        $task = new Task;
        $task->setTitle("test")
            ->setContent("test de content")
            ->setIsDone(1)
            ->setUser($this->entityManager->getRepository(User::class)->find(rand(1, 11)))
            ->setCreatedAt(new DateTime());
        $this->entityManager->getRepository(Task::class)->save($task, true);
        $this->assertSame(true, $task->isIsDone());
    }

    public function testSearchByName()
    {
        $task = $this->entityManager
            ->getRepository(Task::class)
            ->findOneBy(['title' => 'test']);
        $this->assertSame(true, $task->isIsDone());
    }

    public function testRemove()
    {
        $task = $this->entityManager
            ->getRepository(Task::class)
            ->findOneBy(['title' => 'test']);
        $this->entityManager->getRepository(Task::class)->remove($task, true);
        $this->assertSame(null, $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'test']));
    }
    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
