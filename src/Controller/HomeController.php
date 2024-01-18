<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(SessionRepository $sessionRepository, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getEmail()]);

        $sessions = [];
        foreach ($user->getJoinedSessions() as $session) {
            $sessions[] = $this->formatSession($session, 'joined');
        }
        foreach ($user->getCreatedSessions() as $session) {
            $sessions[] = $this->formatSession($session, 'created');
        }

        $nextSession = $sessionRepository->findNextSession($user);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'nextSessions' => $nextSession,
            'sessions' => $sessions
        ]);
    }

    private function formatSession(Session $session, $type = 'joined')
    {
        $date = new \DateTime();
        $color = '#6ea8fe';

        if ($date > $session->getEndTime())
            $color = "#45526F";
        else if ($type == 'joined')
            $color = '#16A370';

        return (object) [
            'id' => $session->getId(),
            'title' => $session->getSubject(),
            'start' => $session->getStartTime()->format('Y-m-d H:i'),
            'end' => $session->getEndTime()->format('Y-m-d H:i'),
            'backgroundColor' => $color,
            'borderColor' => $color,
            'url' => $this->generateUrl('app_session_show', ['id' => $session->getId()])
        ];
    }
}
