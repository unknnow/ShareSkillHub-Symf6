<?php

namespace App\Controller;

use App\Entity\Recommandation;
use App\Entity\User;
use App\Form\RecommandationFormType;
use App\Form\UserProfilFormType;
use App\Form\UserSecurityFormType;
use App\Form\UserSkillsFormType;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $searchValue = $request->get('search');

        if ($searchValue != null) {
            $users = $userRepository->findByDisplayName($searchValue, $this->getUser());
        } else {
            $users = $userRepository->findUsers(false, $this->getUser());
        }

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}', name: 'app_user_consultation', methods: ['GET', 'POST'])]
    public function consultation(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Recommandation Form
        $recommandation = new Recommandation();
        $form = $this->createForm(RecommandationFormType::class, $recommandation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recommandation->setReceiverUser($user);
            $entityManager->persist($recommandation);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_consultation', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        // Sort Categories & Skills
        $categories = [];
        foreach ($user->getSkills() as $skill) {
            $categoryName = $skill->getCategory() ? $skill->getCategory()->getName() : "Autre";

            if (!array_key_exists($categoryName, $categories)) {
                $categories[$categoryName] = [
                    "name" => $categoryName,
                    "color" => $skill->getCategory() ? $skill->getCategory()->getColor() : "#d3d3d3",
                ];
            }

            $categories[$categoryName]["skills"][] = $skill->getName();
        }

        // Check if User has already sended Recommandation
        $alreadySendRecommandation = false;
        foreach ($user->getRecommandations() as $recommandation) {
            if ($recommandation->getSenderUser()->getId() == $this->getUser()->getId()) {
                $alreadySendRecommandation = true;
            }
        }

        return $this->render('user/consultation.html.twig', [
            'user' => $user,
            'categories' => $categories,
            'recommandationForm' => $form->createView(),
            'alreadySendRecommandation' => $alreadySendRecommandation
        ]);
    }

    #[Route('/{id}/profil', name: 'app_user_profil_show', methods: ['GET'])]
    public function showProfil(User $user): Response
    {
        $form = $this->createForm(UserProfilFormType::class, $user);

        return $this->render('user/profil.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'type' => 'show',
        ]);
    }

    #[Route('/{id}/securite', name: 'app_user_security_show', methods: ['GET'])]
    public function showSecurity(User $user): Response
    {
        $form = $this->createForm(UserSecurityFormType::class, $user);

        return $this->render('user/security.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'type' => 'show',
        ]);
    }

    #[Route('/{id}/competences', name: 'app_user_competences', methods: ['GET', 'POST'])]
    public function showCompetences(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserSkillsFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->addSkill($form->get('skill')->getData());
            $entityManager->flush();

            return $this->redirectToRoute('app_user_competences', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/skills.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/sessions', name: 'app_user_sessions', methods: ['GET', 'POST'])]
    public function showSessions(User $user, SessionRepository $sessionRepository): Response
    {
        $createdSessions = $sessionRepository->findAllNextCreatedSessions($user);
        $joinedSessions = $sessionRepository->findAllNextJoinedSessions($user);

        return $this->render('user/sessions.html.twig', [
            'user' => $user,
            'createdSessions' => $createdSessions,
            'joinedSessions' => $joinedSessions,
        ]);
    }

    #[Route('/{id}/recommandations', name: 'app_user_recommandations', methods: ['GET', 'POST'])]
    public function showRecommandations(User $user): Response
    {
        return $this->render('user/recommandations.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/profil/edit', name: 'app_user_profil_edit', methods: ['GET', 'POST'])]
    public function editProfil(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserProfilFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_profil_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/profil.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'type' => 'edit',
        ]);
    }

    #[Route('/{id}/securite/edit', name: 'app_user_security_edit', methods: ['GET', 'POST'])]
    public function editSecurity(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserSecurityFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_security_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/security.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'type' => 'edit',
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_profil_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
    }
}
