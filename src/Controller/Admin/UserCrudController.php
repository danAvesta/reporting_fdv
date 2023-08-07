<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

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
        ->setEntityPermission('ROLE_ADMIN');
    }

    
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('prenom', 'Prénom');
        yield TextField::new('nom');
        yield TextField::new('email');
        yield TextField::new('password', 'Mot de passe')->hideOnIndex()->hideOnDetail()->hideWhenUpdating()->setFormType(PasswordType::class);
        yield ArrayField::new('roles');
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
    
}
