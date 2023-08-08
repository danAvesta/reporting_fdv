<?php

namespace App\Controller\Admin;

use App\Entity\Rdv;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
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
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
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

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {

        $user = $this->getUser();

        if($this->isGranted('ROLE_ADMIN')){
            $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('rdv')
            ->from(Rdv::class, 'rdv');
            return $queryBuilder;
        }

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('rdv')
            ->from(Rdv::class, 'rdv')
            ->where('rdv.commercial = :user')
            ->setParameter('user', $user);

        return $queryBuilder;
    }
    
    public function configureFields(string $pageName): iterable
    {
        // yield IdField::new('id');

        yield DateTimeField::new('dateRdv');
        yield TextField::new('nomMagasin');
        yield TextField::new('contactNom');
        yield TextField::new('contactTel'); 
        yield TextField::new('adresseMagasin');
        yield TextField::new('codePostal');

        if($this->isGranted('ROLE_ADMIN')){
            yield AssociationField::new('commercial')->autocomplete();
        }
        yield DateTimeField::new('createDate')->hideOnForm();
        yield IntegerField::new('statut');
        
        return [
            // IdField::new('createBy')->hideOnForm(),
            
            true
            
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
