<?php

namespace App\Controller\Admin;

use App\Entity\Rdv;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class RdvCrudController extends AbstractCrudController
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
        return Rdv::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setDefaultSort(['dateRdv' => 'ASC'])
        ->setEntityLabelInSingular('Rendez-vous')
        ->setEntityLabelInPlural('Rendez-vous');
    }

    public function configureActions(Actions $actions): Actions
    {
        $addCalendar = Action::new('addCalendar', 'Ajouter au calendrier')
        ->linkToCrudAction('addCalendar');

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $addCalendar)
        ;
    }

    public function addCalendar(AdminContext $context): Response
    {
        $entity = $context->getEntity()->getInstance();
        
        $icsContent = $this->generateIcsContent($entity);
        $response = new Response($icsContent);
        $response->headers->set('Content-Type', 'text/calendar');
        $response->headers->set('Content-Disposition', 'attachment; filename="rdv_'.$entity->getNomMagasin().'.ics"');

        $user=$this->security->getUser()->getEmail();

        $email = (new TemplatedEmail())
            ->from('informatique@avesta.fr')
            ->to($user)
            ->subject('Reporting FDV: nouvel evenement')
            ->text("Bonjour,\r\nVoici le fichier ICS pour le rdv avec le magasin ".$entity->getNomMagasin().".")
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
        $icsContent .= "DESCRIPTION;LANGUAGE=fr-FR:Rendez vous avec " . $entity->getContactNom() . " " . $entity->getContactTel() . "\r\n";
        $icsContent .= "UID:". md5($entity->getNomMagasin()) ."\r\n";
        $icsContent .= "SUMMARY;LANGUAGE=fr-FR:Rendez vous " . $entity->getNomMagasin(). "\r\n";
        $icsContent .= "DTSTART;TZID=Romance Standard Time:".$entity->getDateRdv()->format('Ymd\THis')."\r\n";
        $icsContent .= "DTEND;TZID=Romance Standard Time:".$datefin ."\r\n";
        $icsContent .= "CLASS:PUBLIC\r\n";
        $icsContent .= "PRIORITY:5\r\n";
        $icsContent .= "TRANSP:OPAQUE\r\n";
        $icsContent .= "STATUS:CONFIRMED\r\n";
        $icsContent .= "SEQUENCE:1\r\n";
        $icsContent .= "LOCATION:". $entity->getAdresseMagasin() . " " . $entity->getCodePostal() ."\r\n";
        $icsContent .= "BEGIN:VALARM\r\n";
        $icsContent .= "DESCRIPTION:REMINDER\r\n";
        $icsContent .= "TRIGGER;RELATED=START:-PT30M\r\n";
        $icsContent .= "ACTION:DISPLAY\r\n";
        $icsContent .= "END:VALARM\r\n";
        $icsContent .= "END:VEVENT\r\n";
        $icsContent .= "END:VCALENDAR";

        return $icsContent;
    }
    
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {

        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $user = $this->security->getUser();

        if (!$user) {        
            throw new \Exception('You must be logged in to access this section.');
        }

        if (!in_array('ROLE_ADMIN', $user->getRoles()) && !in_array('ROLE_MANAGER', $user->getRoles())) {
            $queryBuilder->andWhere('entity.commercial = :user');
            $queryBuilder->setParameter('user', $user);
        }
        return $queryBuilder;
    }
    
    public function configureFields(string $pageName): iterable
    {
        // yield IdField::new('id');
        yield DateTimeField::new('dateRdv');
        yield TextField::new('nomMagasin');
        yield TextField::new('contactNom');
        yield TelephoneField::new('contactTel'); 
        yield TextField::new('adresseMagasin');
        yield TextField::new('codePostal');

        if($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_MANAGER')){
            yield AssociationField::new('commercial')->autocomplete();
            yield DateTimeField::new('createDate')->hideOnForm();
        }
        
        yield ChoiceField::new('statut')->setChoices(['En attente' => 0, 'Effectué' => 1, 'Annulé' => 2, 'Reporté' => 3]);
        
        return [
            // IdField::new('createBy')->hideOnForm(),
            
            true
            
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if(!$entityInstance instanceof Rdv) return;
        $user = $this->security->getUser();

        $entityInstance->setCreateBy($user);
        $entityInstance->setCommercial($user);
        $entityInstance->setStatut('0');

        $entityInstance->setCreateDate(new \DateTimeImmutable);

        parent::persistEntity($entityManager, $entityInstance);
    }
    
}
