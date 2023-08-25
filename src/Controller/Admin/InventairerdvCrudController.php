<?php
namespace App\Controller\Admin;

use App\Entity\Inventairerdv;
use App\Entity\InventoryItem; // Import the correct class
use App\Entity\RendezVous;
use App\Form\InventairerdvType;
use Doctrine\ORM\EntityManagerInterface;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
// use App\Form\InventoryItemType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class InventairerdvCrudController extends AbstractCrudController
{
    private $requestStack;
    private $security;

    private $entityManager;

    public function __construct(RequestStack $requestStack, Security $security, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Inventairerdv::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('reference');
        yield IntegerField::new('quantite');
        return [
            // CollectionField::new('')
            //     ->setEntryType(InventairerdvType::class) // Use the correct form type class
            //     ->allowAdd()
            //     ->allowDelete()
        ];
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $rendezvousId = $request->query->get('rendezvousId');
        $rendezvous = null;

        if ($rendezvousId) {
            $rendezvousRepository = $em->getRepository(RendezVous::class);
            $rendezvous = $rendezvousRepository->find($rendezvousId);
        }

       $entityInstance->setDatetime(new \DateTimeImmutable);
       $entityInstance->setIdRdv($rendezvous);
       parent::persistEntity($em, $entityInstance);

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

        $request = $this->requestStack->getCurrentRequest();
        $rendezvousId = $request->query->get('rendezvousId');

        if($this->isGranted("ROLE_ADMIN")){
            $queryBuilder->andWhere('entity.IdRdv = :rendezvousId');
            $queryBuilder->setParameter('rendezvousId', $rendezvousId);
        }
        else{
            $queryBuilder->join('entity.IdRdv', 'rdv');
            $queryBuilder->andWhere('entity.IdRdv = :rendezvousId');
            $queryBuilder->setParameter('rendezvousId', $rendezvousId);
            
            if (!$this->testoriginerdv($rendezvousId, $user)) {
                throw new \Exception("Vous n'êtes pas à l'origine de ce rendez-vous."); // vous pouvez changer cette ligne pour utiliser une exception personnalisée si vous en avez une.
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

    public function configureActions(Actions $actions): Actions 
    {
        return $actions->disable(Action::NEW);
    }
}