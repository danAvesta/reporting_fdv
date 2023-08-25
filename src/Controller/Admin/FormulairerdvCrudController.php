<?php

namespace App\Controller\Admin;

use App\Entity\Formulairerdv;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\RendezVous;
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
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $user = $this->security->getUser();

        if (!$user) {
            
            throw new \Exception('Vous devez être connecté pour accéder à cette section.');
        }

        $request = $this->requestStack->getCurrentRequest();
        $rendezvousId = $request->query->get('rendezvousId');
        
        if($this->isGranted("ROLE_ADMIN")){
            $queryBuilder->andWhere('entity.Idrdv = :rendezvousId');
            $queryBuilder->setParameter('rendezvousId', $rendezvousId);
        }
        else{
            $queryBuilder->join('entity.Idrdv', 'rdv');
            $queryBuilder->andWhere('entity.Idrdv = :rendezvousId');
            $queryBuilder->setParameter('rendezvousId', $rendezvousId);
            
            if (!$this->testoriginerdv($rendezvousId, $user)) {
                throw new \Exception("Vous n'êtes pas à l'origine de ce rendez-vous."); 
            }
        
            $queryBuilder->andWhere('rdv.commercial = :user');
            $queryBuilder->setParameter('user', $user);    
        }
        

        return $queryBuilder;
    }


    
    private function testoriginerdv($rendezvousId, $user) {
        
        $rdvRepository = $this->entityManager->getRepository(RendezVous::class);
        $rdv = $rdvRepository->find($rendezvousId);

        if (!$rdv) {
            return false;
        }
        return $rdv->getCommercial() == $user; 
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            
            IdField::new('id')->hideOnForm(),
            ChoiceField::new('TailleMagasin')
                    ->setChoices([1=>1,2=>2,3=>3,4=>4,5=>5]),
                    
            ChoiceField::new('Marque')
                    ->setChoices(['NEXTBASE'=>'NEXTBASE','JABRA'=>'JABRA','BELKIN'=>'BELKIN']),
                    

            AssociationField::new('Idrdv', 'Rendez-Vous Associé')->hideOnForm(),
           
            ChoiceField::new('Display')
                    ->setChoices(['OUI'=>'OUI','NON'=>'NON']),
            ChoiceField::new('DisplayNon', 'Si c\'est non')
                    ->setChoices(['En magasin'=>'En magasin','N’en dispose
                    pas'=>'N’en dispose
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

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $rendezvousId = $request->query->get('rendezvousId');
        if ($rendezvousId) {
            $rendezvousRepository = $em->getRepository(RendezVous::class);
            $rendezvous = $rendezvousRepository->find($rendezvousId);
            if ($rendezvous) {
                $entityInstance->setIdrdv($rendezvous);
            }
        }

        parent::persistEntity($em, $entityInstance);
    }
}

