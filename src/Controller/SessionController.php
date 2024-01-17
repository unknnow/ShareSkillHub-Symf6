<?php

namespace App\Controller;

use App\Entity\Notation;
use App\Entity\Session;
use App\Form\NotationFormType;
use App\Repository\SessionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/session')]
class SessionController extends AbstractController
{
    #[Route('', name: 'app_session_index', methods: ['GET'])]
    public function index(Request $request, SessionRepository $sessionRepository): Response
    {
        $date = new DateTime($request->get('date'));
        $action = $request->get('action');

        if($action != null) {
            $modifier = ($action === 'next') ? '+1 day' : '-1 day';
            $date = clone $date;
            $date->setISODate($date->format('Y'), $date->format('W'), ($action === 'next') ? 7 : 1)->modify($modifier);
            $this->redirectToRoute('app_session_index', ['date' => $date->format('d-m-Y')]);
        }

        $days = $this->getDaysOfWeek(clone $date);
        $sessions = $sessionRepository->findSessionsOnDate($date, $this->getUser());

        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
            'days' => $days,
            'dayDate' => $date
        ]);
    }

    #[Route('/{id}', name: 'app_session_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    {
        // Notation Form
        $notation = new Notation();
        $form = $this->createForm(NotationFormType::class, $notation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notation->setSession($session);
            $entityManager->persist($notation);
            $entityManager->flush();

            return $this->redirectToRoute('app_session_show', ['id' => $session->getId()], Response::HTTP_SEE_OTHER);
        }

        // Check if User has joined Session
        $hasJoinedSession = false;
        foreach ($session->getParticipantUser() as $user) {
            if ($user->getId() == $this->getUser()->getId()) {
                $hasJoinedSession = true;
            }
        }

        // Check if User has already sended Recommandation
        $alreadySendNotation = false;
        foreach ($session->getNotations() as $notation) {
            if ($notation->getOwnerUser()->getId() == $this->getUser()->getId()) {
                $alreadySendNotation = true;
            }
        }

        return $this->render('session/show.html.twig', [
            'session' => $session,
            'notationForm' => $form->createView(),
            'hasJoinedSession' => $hasJoinedSession,
            'alreadySendNotation' => $alreadySendNotation,
        ]);
    }

    #[Route('/{id}/join', name: 'app_session_join', methods: ['GET'])]
    public function join(Session $session, EntityManagerInterface $em): Response
    {
        $session->addParticipantUser($this->getUser());
        $em->flush();

        return $this->redirectToRoute('app_session_show', ['id' => $session->getId()]);
    }

    #[Route('/{id}/leave', name: 'app_session_leave', methods: ['GET'])]
    public function leave(Session $session, EntityManagerInterface $em): Response
    {
        $session->removeParticipantUser($this->getUser());
        $em->flush();

        return $this->redirectToRoute('app_session_show', ['id' => $session->getId()]);
    }

    private function getDaysOfWeek($date): array
    {
        $year = $date->format('Y');
        $week = $date->format('W');

        $days = [];

        for($i = 1; $i <= 7; $i++) {
            $days[] = clone $date->setISODate($year, $week, $i);
        }

        return $days;
    }
}
