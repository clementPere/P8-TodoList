<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TaskController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine)
    {
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/tasks', name: 'task_list')]
    public function listAction(TaskRepository $task)
    {
        return $this->render('task/list.html.twig', ['tasks' => $task->findAll()]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/tasks-completed', name: 'task_list_completed')]
    public function listActionCompleted(TaskRepository $task)
    {
        return $this->render('task/list.html.twig', ['tasks' => $task->findBy(["isDone" => 1])]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/tasks/create', name: 'task_create')]
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $task->setCreatedAt(new DateTime())
                ->setUser($this->getUser());
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function editAction(Task $task, Request $request)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->getManager()->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTaskAction(Task $task)
    {
        $task->toggle(!$task->isIsDone());
        $this->doctrine->getManager()->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Security("task.getUser() === user", message: "Vous n'avez pas les droits suffisants")]
    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTaskAction(Task $task)
    {
        $em = $this->doctrine->getManager();
        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
