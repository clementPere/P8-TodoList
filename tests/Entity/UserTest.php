<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{

    private $user;
    private $task;

    public function setUp(): void
    {
        $this->user = new User();
        $this->task = new Task();
    }

    public function testUsername(): void
    {
        $this->user->setUsername('testUsername');
        $this->assertSame('testUsername', $this->user->getUsername());
    }

    public function testPassword(): void
    {
        $this->user->setPassword('testPassword');
        $this->assertSame('testPassword', $this->user->getPassword());
    }

    public function testEmail(): void
    {
        $this->user->setEmail('test@test.fr');
        $this->assertSame('test@test.fr', $this->user->getEmail());
    }

    public function testRoles(): void
    {
        $this->user->setRoles(['ROLE_USER']);
        $this->assertSame(['ROLE_USER'], $this->user->getRoles());
    }

    public function testGetTasks(): void
    {
        $tasks = $this->user->getTasks($this->task->getUser());
        $this->assertSame($this->user->getTasks(), $tasks);
    }

    public function testaddTask(): void
    {
        $this->user->addtask($this->task);
        $this->assertCount(1, $this->user->getTasks());
    }

    public function testRemoveTask(): void
    {
        $this->user->removeTask($this->task);
        $this->assertEquals(null, $this->task->getUser());
        $this->assertCount(0, $this->user->getTasks());
    }

    public function testSalt(): void
    {
        $this->assertEquals(null, $this->user->getSalt());
    }

    public function testEraseCredential(): void
    {
        $this->assertNull($this->user->eraseCredentials());
    }
}
