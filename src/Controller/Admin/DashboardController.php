<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Message;
use App\Entity\Notation;
use App\Entity\Recommandation;
use App\Entity\Session;
use App\Entity\Skill;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        $url = $adminUrlGenerator->setController(UserCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('ShareSkillHub');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Accueil', 'fa fa-home', 'app_home');

        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);

        yield MenuItem::subMenu('Compétences', 'fas fa-star')->setSubItems([
            MenuItem::linkToCrud('Liste', 'fas fa-list', Skill::class),
            MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Skill::class)->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::subMenu('Catégories', 'fas fa-layer-group')->setSubItems([
            MenuItem::linkToCrud('Liste', 'fas fa-list', Category::class),
            MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Category::class)->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::subMenu('Messages', 'fas fa-message')->setSubItems([
            MenuItem::linkToCrud('Liste', 'fas fa-list', Message::class),
            MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Message::class)->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::subMenu('Recommandations', 'fas fa-comments')->setSubItems([
            MenuItem::linkToCrud('Liste', 'fas fa-list', Recommandation::class),
            MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Recommandation::class)->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::subMenu('Sessions', 'fas fa-users-rectangle')->setSubItems([
            MenuItem::linkToCrud('Liste', 'fas fa-list', Session::class),
            MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Session::class)->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::subMenu('Notations', 'fas fa-hashtag')->setSubItems([
            MenuItem::linkToCrud('Liste', 'fas fa-list', Notation::class),
            MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Notation::class)->setAction(Crud::PAGE_NEW),
        ]);
    }
}
