<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserCrudController extends AbstractCrudController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        // the labels used to refer to this entity in titles, buttons, etc.
        ->setEntityLabelInSingular('Utilisateur')
        ->setEntityLabelInPlural('Utilisateur')
        // ->setEntityPermission('ROLE_ADMIN')
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('prenom', 'Prénom');
        yield TextField::new('nom');
        if($this->isGranted('ROLE_ADMIN')){
            yield TextField::new('email');
        }
        else{
            yield TextField::new('email')->setFormTypeOption('disabled', true);
        }
        yield TextField::new('password', 'Mot de passe')->hideOnIndex()->hideOnDetail()->hideWhenUpdating()->setFormType(PasswordType::class);
        if($this->isGranted('ROLE_ADMIN')){
            yield ChoiceField::new('roles')
                ->setChoices(['ROLE_USER' => 'ROLE_USER','ROLE_ADMIN' => 'ROLE_ADMIN','ROLE_MANAGER' => 'ROLE_MANAGER'])
                ->allowMultipleChoices();
        }
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // if(!$entityInstance instanceof User) return;

        // dd($entityInstance);

        $this->hashPassword($entityInstance);

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function hashPassword(User $user) : void 
    {
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        ));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            // ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
        ;
    }
    public function edit(AdminContext $context)
    {
        $user = $this->getUser();
        $requestedUserId = $context->getEntity()->getPrimaryKeyValue();

        if (!in_array('ROLE_ADMIN', $user->getRoles()) && $user->getId() !== $requestedUserId) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette section.');
        }

        return parent::edit($context);
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
    
}
