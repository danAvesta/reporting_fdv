<?php

declare(strict_types=1);

namespace App\Controller\Admin;
namespace App\Controller\Admin;

use App\Entity\Formulairerdv;
use App\Entity\RendezVous;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;




class FormulairerdvCrudController extends AbstractCrudController
{
    private $entityManager;
    private $security;    
    private $requestStack;

    public static function getEntityFqcn(): string
    {
        return Formulairerdv::class;
    }

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    public function createIndexQueryBuilder(
        $searchDto,
        $entityDto,
        $fields,
        $filters
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $user = $this->security->getUser();
    
        if (!$user) {
                $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette section.');
                throw new \RuntimeException("User not logged in.");
        }
    
        $rendezvousId = $this->requestStack->getCurrentRequest()->query->get('rendezvousId');
    
        // For admin, no restriction, can see all forms.
        if ($this->isGranted("ROLE_ADMIN")) {
            $queryBuilder->andWhere('entity.Idrdv = :rendezvousId')
                         ->setParameter('rendezvousId', $rendezvousId);
            return $queryBuilder;
        }
    
        
        if (!$this->isValidUserForRdv($rendezvousId, $user)) {
                $this->addFlash('danger', "Vous n'avez pas la permission de voir ces formulaires ou ils n'existent pas.");
                throw new \RuntimeException("User not authorized for this RDV.");
            }
    
        $queryBuilder->join('entity.Idrdv', 'rdv')
                     ->andWhere('rdv.commercial = :user')
                     ->andWhere('entity.Idrdv = :rendezvousId')
                     ->setParameter('user', $user)
                     ->setParameter('rendezvousId', $rendezvousId);
    
        return $queryBuilder;
    }

    private function isValidUserForRdv($rendezvousId, $user): bool
    {
        $rdv = $this->entityManager->getRepository(RendezVous::class)->find($rendezvousId);

        return $rdv && $rdv->getCommercial() === $user;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            
            IdField::new('id')->hideOnForm()->hideOnDetail()->hideOnIndex(),
            ChoiceField::new('TailleMagasin')
                    ->setChoices([1=>1,2=>2,3=>3,4=>4,5=>5]),
                    
            ChoiceField::new('Marque')
                    ->setChoices(['NEXTBASE'=>'NEXTBASE','JABRA'=>'JABRA','BELKIN'=>'BELKIN']),
                    

            AssociationField::new('Idrdv', 'Rendez-Vous Associé')->hideOnForm(),
           
            ChoiceField::new('Display')
                    ->setChoices(['OUI'=>'OUI','NON'=>'NON']),
            ChoiceField::new('DisplayNon', 'Si c\'est non')
                    ->setChoices(['En magasin'=>'En magasin','N\'en dispose
                    pas'=>'N\'en dispose
                    pas','Ne sait pas'=>'Ne sait pas','Non installé'=>'Non installé','Retiré'=>'Retiré','Pas présent'=>'Pas présent','Incomplet'=>'Incomplet','Autre'=>'Autre']),

            
    
            ChoiceField::new('Plv', 'PLV')
                    ->setChoices(['OUI'=>'OUI','NON'=>'NON']),

            ChoiceField::new('MotifDeNonPresence', 'Si c\'est non')
                    ->setChoices(['Manque de place'=>'Manque de place','Produit non référencé
                    '=>'Produit non référencé','Refus du magasin'=>'Refus du magasin','Rupture de produit'=>'Rupture de produit','Autre'=>'Autre']),

            
            TextField::new('DemandeDinstalationPlv', 'Demande d\'Installation PLV'),

            ChoiceField::new('DemandeDinstalationPlv', 'Demande d\'Installation PLV')
                    ->setChoices(['OUI'=>'OUI','NON'=>'NON']),

            
            ChoiceField::new('FichePromo', 'Fiche Promo')
                    ->setChoices(['OUI'=>'OUI','NON'=>'NON']),


            ChoiceField::new('RaisonNonPresenceFichePromo', 'Si c\'est non')
                    ->setChoices(['Manque de place'=>'Manque de place','Refus du magasin'=>'Refus du magasin','Rupture de produit'=>'Rupture de produit','Produit non référencé'=>'Produit non référencé','Autre raison'=>'Autre raison']),
            IntegerField::new('RessentiDeLaVisite', 'Ressenti de la Visite'),
            TextareaField::new('RemarqueEnPlus', 'Remarque en Plus'),
        ];
    }

    public function persistEntity($em, $entityInstance): void
    {
        $rendezvousId = $this->requestStack->getCurrentRequest()->query->get('rendezvousId');

        if (!$rendezvousId && $referrer = $this->requestStack->getCurrentRequest()->query->get('referrer')) {
            $referrerParts = parse_url($referrer);
            parse_str($referrerParts['query'] ?? '', $referrerQuery);
            $rendezvousId = $referrerQuery['rendezvousId'] ?? null;
        }

        if (!$this->isGranted("ROLE_ADMIN") && !$this->isValidUserForRdv($rendezvousId, $this->security->getUser())) {
            $this->addFlash('danger', "Vous n'êtes pas autorisé à modifier ce rendez-vous.");
            return;
        }

        $rendezvous = $rendezvousId ? $em->getRepository(RendezVous::class)->find($rendezvousId) : null;

        if ($rendezvous) {
            $entityInstance->setIdRdv($rendezvous);
        }

        parent::persistEntity($em, $entityInstance);
    }
}
