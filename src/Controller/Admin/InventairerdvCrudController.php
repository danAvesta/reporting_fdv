<?php

namespace App\Controller\Admin;

use App\Entity\Inventairerdv;
use App\Entity\RendezVous;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
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
        return [
            TextField::new('reference'),
            IntegerField::new('quantite')
        ];
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        $rendezvousId = $this->requestStack->getCurrentRequest()->query->get('rendezvousId');

        if (!$this->isGranted("ROLE_ADMIN") && !$this->isValidUserForRdv($rendezvousId, $this->security->getUser())) {
            $this->addFlash('danger', "Vous n'êtes pas autorisé à modifier ce rendez-vous.");
            return;
        }

        $rendezvous = $rendezvousId ? $em->getRepository(RendezVous::class)->find($rendezvousId) : null;

        if ($rendezvous) {
            $entityInstance->setIdRdv($rendezvous);
            $entityInstance->setDatetime(new \DateTimeImmutable);
        }

        parent::persistEntity($em, $entityInstance);
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
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

        if (!$rendezvousId && $referrer = $this->requestStack->getCurrentRequest()->query->get('referrer')) {
            $referrerParts = parse_url($referrer);
            parse_str($referrerParts['query'] ?? '', $referrerQuery);
            $rendezvousId = $referrerQuery['rendezvousId'] ?? null;
        }
        
        if ($this->isGranted("ROLE_ADMIN")) {
            $queryBuilder->andWhere('entity.IdRdv = :rendezvousId')
                         ->setParameter('rendezvousId', $rendezvousId);
        } else {
            if (!$this->isValidUserForRdv($rendezvousId, $user)) {
                $this->addFlash('danger', "Vous n'avez pas la permission de voir ces formulaires ou ils n'existent pas.");
                throw new \RuntimeException("User not authorized for this RDV.");
            }

            $queryBuilder->join('entity.IdRdv', 'rdv')
                         ->andWhere('rdv.commercial = :user')
                         ->andWhere('entity.IdRdv = :rendezvousId')
                         ->setParameter('user', $user)
                         ->setParameter('rendezvousId', $rendezvousId);
        }

        return $queryBuilder;
    }

    private function isValidUserForRdv($rendezvousId, $user): bool 
    {
        $rdv = $this->entityManager->getRepository(RendezVous::class)->find($rendezvousId);

        return $rdv && $rdv->getCommercial() === $user;
    }
}
