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

class UserCrudController extends AbstractCrudController
{
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
        yield TextField::new('prenom');
        yield TextField::new('nom');
        yield TextField::new('email');
        yield ArrayField::new('roles');
        return [
            // IdField::new('id'),
            TextField::new('password')->hideOnIndex()->hideOnDetail(),
            ArrayField::new('roles'),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // if(!$entityInstance instanceof User) return;

        // dd($entityInstance);

        parent::persistEntity($entityManager, $entityInstance);
    }
    
}
