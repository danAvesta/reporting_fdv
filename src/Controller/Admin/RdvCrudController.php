<?php

namespace App\Controller\Admin;

use App\Entity\Rdv;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Core\Security;

class RdvCrudController extends AbstractCrudController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return Rdv::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        // the labels used to refer to this entity in titles, buttons, etc.
        ->setEntityLabelInSingular('Rendez-vous')
        ->setEntityLabelInPlural('Rendez-vous');
        // ->setEntityPermission('ROLE_EDITOR');
    }
    
    public function configureFields(string $pageName): iterable
    {
        $roles = [
            'User'   => 'ROLE_USER',
            'Agency' => 'ROLE_AGENCY',
            'Admin'  => 'ROLE_ADMIN'
        ];

        // yield IdField::new('id');

        yield DateTimeField::new('dateRdv');
        yield TextField::new('nomMagasin');
        yield TextField::new('contactNom');
        yield TextField::new('contactTel'); 
        yield TextField::new('adresseMagasin');
        yield TextField::new('codePostal');

        if($this->isGranted('ROLE_ADMIN')){
            yield AssociationField::new('commercial')->autocomplete()->setCrudController(UserCrudController::class);
        }
        yield DateTimeField::new('createDate')->hideOnForm();
        
        return [
            // IdField::new('createBy')->hideOnForm(),
            
            
            // ChoiceField::new('commercial')->autocomplete()->setChoices([$roles]),
            IntegerField::new('statut')
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if(!$entityInstance instanceof Rdv) return;
        $user = $this->security->getUser();

        $entityInstance->setCreateBy($user);
        $entityInstance->setCommercial($user);
        $entityInstance->setStatut('0');

        $entityInstance->setCreateDate(new \DateTimeImmutable);

        parent::persistEntity($entityManager, $entityInstance);

        // dd($entityInstance);
    }
    
}
