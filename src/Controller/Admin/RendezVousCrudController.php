<?php

namespace App\Controller\Admin;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use App\Entity\RendezVous;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use phpDocumentor\Reflection\Types\Void_;
use Symfony\Component\Security\Core\Security;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use App\Form\RendezVousFormType;
use Symfony\Component\HttpFoundation\Request;
class RendezVousCrudController extends AbstractCrudController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    

    public static function getEntityFqcn(): string
    {
        return RendezVous::class;
    }

    
    

    public function configureFields(string $pageName): iterable
    {
        
        $user = $this->security->getUser();
        if ($user && in_array('ROLE_ADMIN', $user->getRoles())) {
            return [
                IdField::new('id')->hideOnForm()->hideOnDetail()->hideOnIndex(),
                //IdField::new('IdUser')->hideOnForm(),
                TextField::new('NomEnseigne'),
                TextField::new('Ville'),
                NumberField::new('CodePostal'),
                TextField::new('adresse'),
                TextField::new('ContactNom'),
                TelephoneField::new('ContactNumero'),
                DateTimeField::new('DateRdv'),
                DateTimeField::new('DateCreation')->hideOnForm(),
                DateTimeField::new('DateUpdate')->hideOnForm(),
                AssociationField::new('commercial'), 
            ];

        }else{
            return [
                IdField::new('id')->hideOnForm(),
                //IdField::new('IdUser')->hideOnForm(),
                TextField::new('NomEnseigne'),
                TextField::new('Ville'),
                NumberField::new('CodePostal'),
                TextField::new('adresse'),
                TextField::new('ContactNom'),
                TelephoneField::new('ContactNumero'),
                DateTimeField::new('DateRdv'),
                DateTimeField::new('DateCreation')->hideOnForm(),
                DateTimeField::new('DateUpdate')->hideOnForm(),
                AssociationField::new('commercial')->hideOnDetail()->hideOnForm()->hideOnIndex(),
            ];
        }
        
        
    }
    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $user = $this->security->getUser();

        if (!$user) {
            
            throw new \Exception('You must be logged in to access this section.');
        }

        if (!in_array('ROLE_ADMIN', $user->getRoles()) && in_array('ROLE_USER', $user->getRoles())) {
            $queryBuilder->andWhere('entity.commercial = :user');
            $queryBuilder->setParameter('user', $user);
        }

        return $queryBuilder;
    }
    
    public function persistEntity(EntityManagerInterface $em, $entityInstance) : void
    {
        if(!$entityInstance instanceof RendezVous) return;

        $user = $this->security->getUser();

        if(!$user) {
            
            throw new \Exception('You must be logged in to create a rendezvous.');
        }

        $entityInstance->setDateCreation(new \DateTimeImmutable);
        $entityInstance->setDateUpdate(new \DateTimeImmutable);
        $entityInstance->setCommercial($user);

        parent::persistEntity($em, $entityInstance);
    }
    public function configureActions(Actions $actions): Actions
{
    $openForm = Action::new('OpenForm', 'Formulaire de Rendez-Vous', 'fa fa-file-alt')
        ->linkToCrudAction('openForm')
        ->addCssClass('btn btn-primary');

    return $actions
        ->add(Crud::PAGE_INDEX, $openForm);
}
public function openForm(Request $request, RendezVous $rendezVous)
{
    $form = $this->createForm(RendezVousFormType::class, $rendezVous);
    
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
     

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('....');
    }

    return $this->render('.....html.twig', [
        'form' => $form->createView(),
    ]);
}

    
}
