<?php
namespace App\Controller\Admin;

use App\Entity\Inventairerdv;
use App\Entity\InventoryItem; // Import the correct class
use App\Entity\RendezVous;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use App\Form\InventoryItemType;
use Symfony\Component\HttpFoundation\RequestStack;

class InventairerdvCrudController extends AbstractCrudController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public static function getEntityFqcn(): string
    {
        return Inventairerdv::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            CollectionField::new('items')
                ->setEntryType(InventoryItemType::class) // Use the correct form type class
                ->allowAdd()
                ->allowDelete()
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

        $datetime = new \DateTime();
        
        foreach ($entityInstance->getItems() as $item) {
            $item->setInventairerdv($entityInstance);
            $item->setDatetime($datetime);

            if ($rendezvous) {
                $item->setIdrdv($rendezvous);
            }

            $em->persist($item);
        }

        parent::persistEntity($em, $entityInstance);
    }
}