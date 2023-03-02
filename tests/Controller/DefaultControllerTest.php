<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    /**
     *
     * @return void
     */
    public function authenticateAdmin(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $getUser = $userRepository->findOneBy(['email' => 'admin@gmail.com']);
        $this->client->loginUser($getUser);
    }

    public function authenticateUser(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $getUser = $userRepository->findOneBy(['email' => 'user@gmail.com']);
        $this->client->loginUser($getUser);
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
