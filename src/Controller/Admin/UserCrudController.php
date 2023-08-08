<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;


use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;

class UserCrudController extends AbstractCrudController
{
    
    
    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    

    
    public function configureFields(string $pageName): iterable
    {
        $user = $this->getUser();
        if ($user && in_array('ROLE_ADMIN', $user->getRoles())) {
            return [
                IdField::new('id')->hideOnForm(),
                TextField::new('Nom'),
                TextField::new('Prenom'),
                EmailField::new('email'),
                TextField::new('password', 'Mot de passe')->hideOnIndex()->hideOnDetail()->hideWhenUpdating()->setFormType(PasswordType::class),
                ArrayField::new('roles'),
            ];
        }else{
            return [
                IdField::new('id')->hideOnIndex()->hideOnDetail()->hideWhenUpdating()->hideOnForm(),
                TextField::new('Nom'),
                TextField::new('Prenom'),
                EmailField::new('email'),
                TextField::new('password', 'Mot de passe')->hideOnIndex()->hideOnDetail()->hideWhenUpdating()->setFormType(PasswordType::class),
                ArrayField::new('roles')->hideOnIndex()->hideOnDetail()->hideWhenUpdating()->hideOnForm(),
            ];
        }
    }
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashPassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    private function hashPassword(User $user): void
    {
        $user->setPassword($this->passwordEncoder->hashPassword(
            $user,
            $user->getPassword()
        ));
    }
    public function configureActions(Actions $actions): Actions
    {
        $user = $this->getUser();

        if ($user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            
            $actions->disable(Action::INDEX, Action::NEW, Action::DELETE);
        }

        return $actions;
    }

    public function index(AdminContext $context)
    {
        $user = $this->getUser();

        if ($user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette section.');
        }

        return parent::index($context);
    }
    
    
}
