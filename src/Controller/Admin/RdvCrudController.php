<?php

namespace App\Controller\Admin;

use App\Entity\Rdv;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RdvCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rdv::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        // the labels used to refer to this entity in titles, buttons, etc.
        ->setEntityLabelInSingular('Rendez-vous')
        ->setEntityLabelInPlural('Rendez-vous')
        ->setEntityPermission('ROLE_EDITOR');
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('createBy')->hideOnForm(),
            DateTimeField::new('dateRdv'),
            DateTimeField::new('createDate')->hideOnForm(),
            TextField::new('nomMagasin'),
            TextEditorField::new('adresseMagasin'),
            TextField::new('commercial'),
            BooleanField::new('statut')
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if(!$entityInstance instanceof Rdv) return;

        $entityInstance->setCreateDate(new \DateTimeImmutable);

        parent::persistEntity($entityManager, $entityInstance);

        // dd($entityInstance);
    }
    
}
