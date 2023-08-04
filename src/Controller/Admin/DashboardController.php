<?php

namespace App\Controller\Admin;

use App\Entity\RendezVous;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator){
    }
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url =$this->adminUrlGenerator->setController(RendezVousCrudController::class)->generateUrl();
        return $this->redirect($url);

       
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Reporting Fdv');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Page d\'accueil', 'fa fa-home');
        yield MenuItem::linkToCrud('Ajouter un Rendez Vous', 'fas fa-plus', RendezVous::class)->setAction(Crud::PAGE_NEW);
        yield MenuItem::section('Administration');
        yield MenuItem::subMenu('User', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Ajouter un User', 'fas fa-plus', User::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Liste des User', 'fas fa-eye', User::class)
        ]);
        
        
    }
}
