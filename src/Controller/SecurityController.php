<?php

// src/Controller/SecurityController.php
namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/logout', name: 'app_logout')]
    public function logout(): never
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/oubli-pass', name:'forgotten_password')]
    public function forgottenPassword(
        Request $request,
        UserRepository $usersRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        // Assurez-vous d'avoir une classe SendMailService ou remplacez-la par votre service de messagerie.
        SendMailService $mail  
    ): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // On va chercher l'utilisateur par son email
            $user = $usersRepository->findOneByEmail($form->get('email')->getData());

            // On vérifie si on a un utilisateur
            if($user){
                // On génère un token de réinitialisation
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);

                // Set the expiration date for 15 minutes from now
                $expiresAt = new \DateTimeImmutable('+15 minutes');
                $user->setResetTokenExpiresAt($expiresAt);

                $entityManager->persist($user);
                $entityManager->flush();

                // On génère un lien de réinitialisation du mot de passe
                $url = $this->generateUrl('reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                
                // Envoi du mail
                $mail->send(
                    'informatique@avesta.fr',
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
                    'password_reset',
                    ['url' => $url, 'user' => $user]
                );

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');
            }
            
            // $user est null
            $this->addFlash('danger', 'Aucun compte avec ce mail');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }

    #[Route('/oubli-pass/{token}', name:'reset_pass')]
    public function resetPass(string $token, Request $request, UserRepository $usersRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $usersRepository->findOneByResetToken($token);

        // On vérifie si l'utilisateur existe
        $user = $usersRepository->findOneByResetToken($token);

        if (!$user) {
            $this->addFlash('danger', 'Votre demande a expiré ou le jeton est invalide.');
            return $this->redirectToRoute('app_login');
        }

        $now = new \DateTime();
        if ($user->getResetTokenExpiresAt() <= $now) {
            $this->addFlash('danger', 'Votre demande de réinitialisation a expiré. Veuillez demander à nouveau.');
            return $this->redirectToRoute('app_login');
        }

        if($user){
            $form = $this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                // On efface le token
                $user->setResetToken(NULL);
                $user->setResetTokenExpiresAt(NULL);

                // On enregistre le nouveau mot de passe en le hashant
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe changé avec succès');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('security/reset_password.html.twig', [
                'passForm' => $form->createView()
            ]);
        }
        
        // Si le token est invalide on redirige vers le login
        $this->addFlash('danger', 'Jeton invalide');
    }
}