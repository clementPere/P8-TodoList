<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends AbstractController
{

    public function __construct(private ManagerRegistry $doctrine, private UserPasswordHasherInterface $hasher)
    {
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users', name: 'user_list')]
    public function listAction(UserRepository $user)
    {
        return $this->render('user/list.html.twig', ['users' => $user->findAll()]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/create', name: 'user_create')]
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $password = $this->hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }


    #[Route('/users/{id}/edit', name: 'user_edit')]
    #[Security("is_granted('ROLE_ADMIN') or currentUser === user", message: "Vous n'avez pas les droits suffisants")]
    public function editAction(User $currentUser, Request $request)
    {
        $form = $this->createForm(UserType::class, $currentUser);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $form->getData();
            $password = $this->hasher->hashPassword($currentUser, $currentUser->getPassword());
            $currentUser->setPassword($password);
            $em = $this->doctrine->getManager();
            $em->persist($currentUser);
            $em->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");
            return $this->redirectToRoute('app_home');
        }
        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $currentUser]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/{id}/delete', name: 'user_delete')]
    public function deleteTaskAction(User $user, UserRepository $userRepository)
    {
        $em = $this->doctrine->getManager();

        $anonymeUser = $userRepository->findOneBy(["email" => "anonyme@gmail.com"]);
        foreach ($user->getTasks() as $key => $value) {
            $value->setUser($anonymeUser);
        }
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', 'L\'utilisateur a bien été supprimée.');
        return $this->redirectToRoute('user_list');
    }
}
