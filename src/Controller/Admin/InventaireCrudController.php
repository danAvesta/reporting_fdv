<?php

namespace App\Controller\Admin;

use App\Entity\Inventaire;
use App\Entity\Rdv;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class InventaireCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Inventaire::class;
    }

    private $requestStack;
    private $security;
    private $entityManager;

    public function __construct(RequestStack $requestStack, Security $security, EntityManagerInterface $entityManager){
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        // ->setDefaultSort(['dateRdv' => 'ASC'])
        ->setEntityLabelInSingular('Inventaire')
        ->setEntityLabelInPlural('Inventaires')
        ;
    }

    public function configureActions(Actions $actions): Actions 
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function(Action $action){
                return $action->setLabel('Ajouter un produit');
            })
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('produit'),
            IntegerField::new('quantite'),
            // TextEditorField::new('description'),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        
        $idRdv = $this->requestStack->getCurrentRequest()->query->get('idRdv');

        if (!$idRdv && $referrer = $this->requestStack->getCurrentRequest()->query->get('referrer')) {
            $referrerParts = parse_url($referrer);
            parse_str($referrerParts['query'] ?? '', $referrerQuery);
            $idRdv = $referrerQuery['idRdv'] ?? null;
        }

        $rdv = $idRdv ? $entityManager->getRepository(Rdv::class)->find($idRdv) : null;
        $entityInstance->setDateInventaire(new \DateTimeImmutable);
        $entityInstance->setIdRdv($rdv);

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, $fields, $filters): QueryBuilder
    {

        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $user = $this->security->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette section.');
            throw new \RuntimeException("User not logged in.");
        }

        $idRdv = $this->requestStack->getCurrentRequest()->query->get('idRdv');

        if (!$idRdv && $referrer = $this->requestStack->getCurrentRequest()->query->get('referrer')) {
            $referrerParts = parse_url($referrer);
            parse_str($referrerParts['query'] ?? '', $referrerQuery);
            $idRdv = $referrerQuery['idRdv'] ?? null;
        }
        
        if ($this->isGranted("ROLE_ADMIN")) {
            $queryBuilder
                ->andWhere('entity.idRdv = :rendezvousId')
                ->setParameter('rendezvousId', $idRdv);
        }
        else {
            $rdv = $idRdv ? $this->entityManager->getRepository(Rdv::class)->find($idRdv) : null;
            // dd($user);
            if (!$rdv || $rdv->getCommercial()!=$user) {
                $this->addFlash('danger', "Vous n'avez pas la permission de voir ces formulaires ou ils n'existent pas.");
                throw new \RuntimeException("User not authorized for this RDV.");
            }
            
            $queryBuilder
                ->join('entity.idRdv', 'rdv')
                ->andWhere('rdv.commercial = :user')
                ->andWhere('entity.idRdv = :idRdv')
                ->setParameter('user', $user)
                ->setParameter('idRdv', $idRdv);
        }
        
        return $queryBuilder;
    }
    
}
