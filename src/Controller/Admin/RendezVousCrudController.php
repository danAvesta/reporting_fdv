<?php

namespace App\Controller\Admin;

use App\Entity\Formulairerdv;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
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
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use App\Form\RendezVousFormType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
class RendezVousCrudController extends AbstractCrudController
{
    private $security;
    private $mailer;

    public function __construct(Security $security, MailerInterface $mailer)
    {
        $this->security = $security;
        $this->mailer = $mailer;
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
                AssociationField::new('commercial')->hideOnDetail()->hideOnForm()->hideOnIndex(),
            ];
        }
        
        
    }
    public function addCalendar(AdminContext $context): Response
    {
        $entity = $context->getEntity()->getInstance();
        
        $icsContent = $this->generateIcsContent($entity);
        $response = new Response($icsContent);
        $response->headers->set('Content-Type', 'text/calendar');
        $response->headers->set('Content-Disposition', 'attachment; filename="rdv_'.$entity->getNomEnseigne().'.ics"');

        $user=$this->security->getUser()->getEmail();

        $email = (new TemplatedEmail())
            ->from('informatique@avesta.fr')
            ->to($user)
            ->subject('Reporting FDV: nouvel evenement')
            ->text("Bonjour,\r\nVoici le fichier ICS pour le rdv avec le magasin ".$entity->getNomEnseigne().".")
            ->attach($icsContent, 'rdv_avesta.ics', 'text/calendar');
        
            $this->mailer->send($email);

        return $response;

    }

    private function generateIcsContent($entity)
    {
        $datefin = $entity->getDateRdv()->format('Ymd\THis');
        $dateFin = \DateTime::createFromFormat('Ymd\THis',$datefin);
        $dateFin->modify('+30 minutes');
        $datefin=$dateFin->format('Ymd\THis');

        $icsContent = "BEGIN:VCALENDAR\r\n";
        $icsContent .= "METHOD:PUBLISH\r\n";
        $icsContent .= "PRODID:Microsoft Exchange Server 2010\r\n";
        $icsContent .= "VERSION:2.0\r\n";
        $icsContent .= "BEGIN:VTIMEZONE\r\n";
        $icsContent .= "TZID:Romance Standard Time\r\n";
        $icsContent .= "BEGIN:STANDARD\r\n";
        $icsContent .= "DTSTART:20190306T150000Z\r\n";
        $icsContent .= "TZOFFSETFROM:+0200\r\n";
        $icsContent .= "TZOFFSETTO:+0100\r\n";
        $icsContent .= "RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=10\r\n";
        $icsContent .= "END:STANDARD\r\n";
        $icsContent .= "BEGIN:DAYLIGHT\r\n";
        $icsContent .= "DTSTART:16010101T020000\r\n";
        $icsContent .= "TZOFFSETFROM:+0100\r\n";
        $icsContent .= "TZOFFSETTO:+0200\r\n";
        $icsContent .= "RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=3\r\n";
        $icsContent .= "END:DAYLIGHT\r\n";
        $icsContent .= "END:VTIMEZONE\r\n";
        $icsContent .= "BEGIN:VEVENT\r\n";
        $icsContent .= "DESCRIPTION;LANGUAGE=fr-FR:Rendez vous avec " . $entity->getContactNom() . " " . $entity->getContactNumero() . "\r\n";
        $icsContent .= "UID:". md5($entity->getNomEnseigne()) ."\r\n";
        $icsContent .= "SUMMARY;LANGUAGE=fr-FR:Rendez vous " . $entity->getNomEnseigne(). "\r\n";
        $icsContent .= "DTSTART;TZID=Romance Standard Time:".$entity->getDateRdv()->format('Ymd\THis')."\r\n";
        $icsContent .= "DTEND;TZID=Romance Standard Time:".$datefin ."\r\n";
        $icsContent .= "CLASS:PUBLIC\r\n";
        $icsContent .= "PRIORITY:5\r\n";
        $icsContent .= "TRANSP:OPAQUE\r\n";
        $icsContent .= "STATUS:CONFIRMED\r\n";
        $icsContent .= "SEQUENCE:1\r\n";
        $icsContent .= "LOCATION:". $entity->getAdresse() . " " . $entity->getCodePostal() ."\r\n";
        $icsContent .= "BEGIN:VALARM\r\n";
        $icsContent .= "DESCRIPTION:REMINDER\r\n";
        $icsContent .= "TRIGGER;RELATED=START:-PT30M\r\n";
        $icsContent .= "ACTION:DISPLAY\r\n";
        $icsContent .= "END:VALARM\r\n";
        $icsContent .= "END:VEVENT\r\n";
        $icsContent .= "END:VCALENDAR";

        return $icsContent;
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
    $addCalendar = Action::new('addCalendar', 'Ajouter au calendrier')
    ->linkToCrudAction('addCalendar');

    $newFormAction = Action::new('NouveauFormulaire','Formulaire Reporting')
        ->linkToCrudAction('NouveauFormulaire');

    $InventaireFormAction = Action::new('InventaireFormulaire','Formulaire Inventaire')
        ->linkToCrudAction('InventaireFormulaire');

    $InventaireDetail = Action::new('InventaireDetail','Détail Inventaire')
    ->linkToCrudAction('InventaireDetail');

    $ReportingDetail = Action::new('ReportingDetail','Détail Reporting')
    ->linkToCrudAction('ReportingDetail');

    return $actions
        ->add(Crud::PAGE_INDEX, $addCalendar)
        ->add(Crud::PAGE_INDEX, $newFormAction)
        ->add(Crud::PAGE_INDEX, $InventaireFormAction)
        ->add(Crud::PAGE_INDEX, $InventaireDetail)
        ->add(Crud::PAGE_INDEX, $ReportingDetail)
        ->add(Crud::PAGE_INDEX, Action::DETAIL);
        
    }

    public function NouveauFormulaire(AdminContext $context): Response
    {
        $rendezvousId = $context->getEntity()->getPrimaryKeyValue();
        return $this->redirectToRoute('admin', [
            'crudAction' => 'new',
            'crudControllerFqcn' => FormulairerdvCrudController::class,
            'rendezvousId' => $rendezvousId,
        ]);
    }
    public function InventaireFormulaire(AdminContext $context): Response
    {
        $rendezvousId = $context->getEntity()->getPrimaryKeyValue();
        return $this->redirectToRoute('admin', [
            'crudAction' => 'new',
            'crudControllerFqcn' => InventairerdvCrudController::class,
            'rendezvousId' => $rendezvousId,
        ]);
    }

    public function InventaireDetail(AdminContext $context): Response
    {
        $rendezvousId = $context->getEntity()->getPrimaryKeyValue();
        return $this->redirectToRoute('admin',[
            'crudAction' => 'index',
            'crudControllerFqcn' => InventairerdvCrudController::class,
            'rendezvousId' => $rendezvousId,
        ]);
    }

    public function ReportingDetail(AdminContext $context): Response
    {
        $rendezvousId = $context->getEntity()->getPrimaryKeyValue();
        return $this->redirectToRoute('admin',[
            'crudAction' => 'index',
            'crudControllerFqcn' => FormulairerdvCrudController::class,
            'rendezvousId' => $rendezvousId,
        ]);
    }
}
