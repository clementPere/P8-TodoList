<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;

class UserControllerTest extends DefaultControllerTest
{
    public function testIfNotLogin(): void
    {
        $this->client->request('GET', '/users');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("/login", parse_url($this->client->followRedirect()->getUri(), PHP_URL_PATH));
    }

    public function testIfLoginAsUser(): void
    {
        $this->authenticateUser();
        $this->client->request('GET', '/users');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testIfLoginAsAdmin(): void
    {
        $this->authenticateAdmin();
        $this->client->request('GET', '/users');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateUserIfNotLogin(): void
    {
        $this->client->request('GET', '/users/create');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("/login", parse_url($this->client->followRedirect()->getUri(), PHP_URL_PATH));
    }

    public function testCreateUserIfLoginAsUser(): void
    {
        $this->authenticateUser();
        $this->client->request('GET', '/users/create');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateUserIfLoginAsAdmin(): void
    {
        $this->authenticateAdmin();
        $crawler = $this->client->request('GET', '/users/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'TestUser';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'test@gmail.com';
        $form['user[roles]'] = 'ROLE_USER';
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testUpdateUserIfNotLogin(): void
    {
        $id = $this->getUserId();
        $this->client->request('GET', "/users/$id/edit");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("/login", parse_url($this->client->followRedirect()->getUri(), PHP_URL_PATH));
    }

    public function testUpdateUserLoginAsUserButNotOwner(): void
    {
        $this->authenticateUser();
        $id = $this->getUserId();
        $this->client->request('GET', "/users/$id/edit");
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateUserLoginAsUserAndOwner(): void
    {
        $this->authenticateUser();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $getUser = $userRepository->findOneBy(["email" => "user@gmail.com"]);
        $getUser = $getUser->getId();

        $crawler = $this->client->request('GET', "/users/$getUser/edit");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'TestUpdateUser';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'user@gmail.com';
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testUpdateUserLoginAsAdmin(): void
    {
        $this->authenticateAdmin();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $getUser = $userRepository->findOneBy(["email" => "user@gmail.com"]);
        $getUserId = $getUser->getId();

        $crawler = $this->client->request('GET', "/users/$getUserId/edit");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'TestUpdateUser';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'user@gmail.com';
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testDeleteUserIfNotConnected()
    {
        $id = $this->getUserId();
        $this->client->request('GET', "/users/$id/delete");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("/login", parse_url($this->client->followRedirect()->getUri(), PHP_URL_PATH));
    }

    public function testDeleteUserIfConnectedAsUser()
    {
        $id = $this->getUserId();
        $this->client->request('GET', "/users/$id/delete");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteUserIfConnectedAsAdmin()
    {
        $this->authenticateAdmin();
        $id = $this->getUserId();
        $this->client->request('GET', "/users/$id/delete");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    private function getUserId(): int
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $getUser = $userRepository->findOneBy([], ['id' => 'desc']);
        return $getUser->getId();
    }
}
