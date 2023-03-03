<?php

namespace App\Tests\Repository;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
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
        $user = new User;
        $user->setEmail("testtest@test.com")
            ->setRoles(["ROLE_USER"])
            ->setPassword("test")
            ->setUsername("test");
        $this->entityManager->getRepository(User::class)->save($user, true);
        $this->assertSame("testtest@test.com", $user->getEmail());
    }

    public function testSearchByName()
    {
        $this->assertSame("testtest@test.com", $this->getUser()->getEmail());
    }

    public function testRemove()
    {
        $this->entityManager->getRepository(User::class)->remove($this->getUser(), true);
        $this->assertSame(null, $this->getUser());
    }

    private function getUser()
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'testtest@test.com']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
