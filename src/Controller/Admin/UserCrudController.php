<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


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
                ChoiceField::new('roles')
                    ->setChoices(['ROLE_USER'=>'ROLE_USER','ROLE_ADMIN'=>'ROLE_ADMIN','ROLE_MANAGER'=>'ROLE_MANAGER'])
                    ->allowMultipleChoices(),
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
       

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette section.');
        }

        return parent::index($context);
    }
    public function detail(AdminContext $context)
    {
        $user = $this->getUser();
        $requestedId = $context->getEntity()->getInstance()->getId();

        // Si ce n'est pas un admin et qu'il tente d'accéder à un autre profil
        if (!in_array('ROLE_ADMIN', $user->getRoles()) && $user->getId() !== $requestedId) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette section.');
        }

        return parent::detail($context);
    }
    public function edit(AdminContext $context)
    {
        $user = $this->getUser();
        $requestedUserId = $context->getEntity()->getPrimaryKeyValue();

        if (!in_array('ROLE_ADMIN', $user->getRoles())  && $user->getId() !== $requestedUserId) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette section.');
        }

        return parent::edit($context);
    }
    
    
}
