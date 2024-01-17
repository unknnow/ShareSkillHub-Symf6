<?php

namespace App\Controller;

use App\Entity\Recommandation;
use App\Entity\User;
use App\Form\RecommandationFormType;
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
}
