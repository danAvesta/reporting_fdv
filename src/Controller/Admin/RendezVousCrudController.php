<?php

namespace App\Controller\Admin;

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
        return [
            IdField::new('id')->hideOnForm(),
            IdField::new('IdUser')->hideOnForm(),
            TextField::new('NomEnseigne'),
            TextField::new('Ville'),
            NumberField::new('CodePostal'),
            TextField::new('ContactNom'),
            TelephoneField::new('ContactNumero'),
            DateTimeField::new('DateRdv'),
            DateTimeField::new('DateCreation')->hideOnForm(),
            DateTimeField::new('DateUpdate')->hideOnForm(),
        ];
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance) : void
    {
        if(!$entityInstance instanceof RendezVous) return;

        $user = $this->security->getUser();
        if(!$user) return;

        $entityInstance->setIdUser($user->getId());
        $entityInstance->setDateCreation(new \DateTimeImmutable);
        $entityInstance->setDateUpdate(new \DateTimeImmutable);

        parent::persistEntity($em, $entityInstance);
    }
}
