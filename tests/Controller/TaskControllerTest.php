<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;

use function PHPUnit\Framework\assertEquals;

class TaskControllerTest extends DefaultControllerTest
{
    public function testIfNotLogin(): void
    {
        $this->client->request('GET', '/tasks');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("/login", parse_url($this->client->followRedirect()->getUri(), PHP_URL_PATH));
    }

    public function testIfLogin(): void
    {
        $this->authenticateUser();
        $this->client->request('GET', '/tasks');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateIfNotLogin(): void
    {
        // $this->authenticateUser();
        $this->client->request('GET', '/tasks/create');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("/login", parse_url($this->client->followRedirect()->getUri(), PHP_URL_PATH));
    }

    public function testCreateIfLogin(): void
    {
        $this->authenticateUser();
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Test du title';
        $form['task[content]'] = 'Test du content';
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testUpdateIfNotLogin(): void
    {
        $id = $this->getTaskId();
        $this->client->request('GET', "/tasks/$id/edit");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("/login", parse_url($this->client->followRedirect()->getUri(), PHP_URL_PATH));
    }

    public function testUpdateIfLogin(): void
    {
        $this->authenticateUser();
        $id = $this->getTaskId();
        $crawler = $this->client->request('GET', "/tasks/$id/edit");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'Modification du title';
        $form['task[content]'] = 'Modification du content';
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testTaskDoneIfNotConnected()
    {
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $getTask = $taskRepository->findOneBy([], ['id' => 'desc']);
        $getTaskId = $getTask->getId();
        $this->client->request('GET', "/tasks/$getTaskId/toggle");
        $this->assertEquals(false, $getTask->isIsDone());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testTaskDoneIfConnected()
    {
        $this->authenticateUser();
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $getTask = $taskRepository->findOneBy([], ['id' => 'desc']);
        $getTaskId = $getTask->getId();
        $this->client->request('GET', "/tasks/$getTaskId/toggle");
        $this->assertEquals(true, $getTask->isIsDone());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteTaskIfNotConnected()
    {
        $id = $this->getTaskId();
        $this->client->request('GET', "/tasks/$id/delete");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("/login", parse_url($this->client->followRedirect()->getUri(), PHP_URL_PATH));
    }

    public function testDeleteTaskIfConnectedButNotOwner()
    {
        $this->authenticateAdmin();
        $id = $this->getTaskId();
        $this->client->request('GET', "/tasks/$id/delete");
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteTaskIfConnectedAndOwner()
    {
        $this->authenticateUser();
        $id = $this->getTaskId();
        $this->client->request('GET', "/tasks/$id/delete");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $this->client->followRedirect()->filter('div.alert-success')->count());
    }

    public function testTaskCompletedIfNotConnected()
    {
        $this->client->request('GET', "/tasks-completed");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("/login", parse_url($this->client->followRedirect()->getUri(), PHP_URL_PATH));
    }

    public function testTaskCompletedIfConnected()
    {
        $this->authenticateUser();
        $this->client->request('GET', "/tasks-completed");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    private function getTaskId(): int
    {
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $getTask = $taskRepository->findOneBy([], ['id' => 'desc']);
        return $getTask->getId();
    }
}
