<?php

namespace App\Controller\Admin;

use App\Entity\Formulairerdv;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\RendezVous;

class FormulairerdvCrudController extends AbstractCrudController
{
    private $entityManager;
    private $requestStack;

    public static function getEntityFqcn(): string
    {
        return Formulairerdv::class;
    }

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            
            IdField::new('id')->hideOnForm(),
            IntegerField::new('TailleMagasin'),
            AssociationField::new('Idrdv', 'Rendez-Vous Associé')->hideOnForm(),
            TextField::new('Marque'),
            TextField::new('Display'),
            TextField::new('Reference', 'Référence'),
            TextField::new('Quantite', 'Quantité'),
            TextField::new('Plv', 'PLV'),
            TextField::new('MotifDeNonPresence', 'Motif de Non Présence'),
            TextField::new('DemandeDinstalationPlv', 'Demande d\'Installation PLV'),
            TextField::new('FichePromo', 'Fiche Promo'),
            TextField::new('RaisonNonPresenceFichePromo', 'Raison Non Présence Fiche Promo'),
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

