<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

class UserCrudController extends AbstractCrudController
{
    public function index(AdminContext $context): Response
    {
        $user = $this->getUser(); 

       
        if (!$user || !in_array('ROLE_ADMIN', $user->getRoles())) {
            throw new AccessDeniedException();
        }

       
        return parent::index($context);
    }
    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    public function configureCrud(Crud $crud):Crud{
        return $crud->setEntityPermission("ROLE_ADMIN");
    }
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('Nom'),
            TextField::new('Prenom'),
            EmailField::new('email'),
            TextField::new('password', 'Mot de passe')->hideOnIndex()->hideOnDetail()->hideWhenUpdating()->setFormType(PasswordType::class),
            ArrayField::new('roles'),
        ];
    }
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashPassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    private function hashPassword(User $user): void
    {
        $user->setPassword($this->passwordEncoder->hashPassword(
            $user,
            $user->getPassword()
        ));
    }
}
