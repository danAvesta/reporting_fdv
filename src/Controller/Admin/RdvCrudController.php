<?php

namespace App\Controller\Admin;

use App\Entity\Rdv;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
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
        ->setDefaultSort(['dateRdv' => 'ASC'])
        // the labels used to refer to this entity in titles, buttons, etc.
        ->setEntityLabelInSingular('Rendez-vous')
        ->setEntityLabelInPlural('Rendez-vous');
        // ->setEntityPermission('ROLE_EDITOR');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            // ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
        ;
    }
    
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {

        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $user = $this->security->getUser();

        if (!$user) {        
            throw new \Exception('You must be logged in to access this section.');
        }

        if (!in_array('ROLE_ADMIN', $user->getRoles()) && !in_array('ROLE_MANAGER', $user->getRoles())) {
            $queryBuilder->andWhere('entity.commercial = :user');
            $queryBuilder->setParameter('user', $user);
        }
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

        if($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_MANAGER')){
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
