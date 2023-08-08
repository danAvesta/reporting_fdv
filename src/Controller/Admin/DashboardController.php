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
        yield MenuItem::linkToCrud('Ajouter un Rendez Vous', 'fas fa-plus', RendezVous::class)->setAction(Crud::PAGE_NEW);
        
        if ($this->isGranted("ROLE_ADMIN")){
            yield MenuItem::section('Administration');
            yield MenuItem::subMenu('User', 'fas fa-bars')->setSubItems([
                MenuItem::linkToCrud('Ajouter un User', 'fas fa-plus', User::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud('Liste des User', 'fas fa-eye', User::class)
            ]);
            
        }
        
    }
    
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userId = $user->getId();
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        // if you prefer to create the user menu from scratch, use: return UserMenu::new()->...
        return parent::configureUserMenu($user)
            // use the given $user object to get the user name
            

            // you can use any type of menu item, except submenus
            
            ->addMenuItems([
                MenuItem::linkToCrud('Mon profil', 'fa fa-id-card', User::class)->setAction(Crud::PAGE_DETAIL)->setEntityId($userId),
                MenuItem::linkToRoute('Changer le Mot de passe', 'fa-solid fa-key', '...', ['...' => '...']),
                MenuItem::section(),
               
            ]);
    }
    
    
}
