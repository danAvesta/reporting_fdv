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

use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;

use Symfony\Component\Security\Core\User\UserInterface;

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
        if ($this->isGranted("ROLE_MANAGER")){
            yield MenuItem::section('Administration');                
            yield MenuItem::linkToCrud('Liste des User', 'fas fa-eye', User::class)->setAction(Crud::PAGE_INDEX);

            
        }
        if ($this->isGranted("ROLE_ADMIN")){
            yield MenuItem::section('Administration');                
            yield MenuItem::linkToCrud('Liste des User', 'fas fa-eye', User::class)->setAction(Crud::PAGE_INDEX);

            
        }
        
    }
    
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userId = $user->getId();
        
        return parent::configureUserMenu($user)

            
            ->addMenuItems([
                MenuItem::linkToCrud('Mon profil', 'fa fa-id-card', User::class)->setAction(Crud::PAGE_DETAIL)->setEntityId($userId),
                MenuItem::linkToRoute('Changer le Mot de passe', 'fa-solid fa-key', 'change-password'),
                MenuItem::section(),
               
            ]);
    }
    
    
}
