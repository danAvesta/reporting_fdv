<?php

namespace App\Controller;



use App\Form\ChangePasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use ErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Error\Error;
class ChangePasswordController extends AbstractdashboardController
{
    #[Route("/change-password", name:"change-password")]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour changer votre mot de passe.');
        }

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        $newPassword = $form->get('newPassword')->getData();
        $newPasswordConfirmation = $form->get('newPasswordConfirmation')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$passwordEncoder->isPasswordValid($user, $form->get('oldPassword')->getData())) {

                return $this->render('user/change_password.html.twig', ['form' => $form->createView(),"erreur"=>"Mot de passe invalide"]);
                
            } else {

                

                if ($newPassword !== $newPasswordConfirmation) {
                    return $this->render('user/change_password.html.twig', ['form' => $form->createView(),"erreur"=>"Le mot de passe de confirmation est différent"]);
                }
                $newPassword = $passwordEncoder->hashPassword($user, $form->get('newPassword')->getData());
                $user->setPassword($newPassword);
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('admin');
            }
        }

        return $this->render('user/change_password.html.twig', ['form' => $form->createView()]);
    }
}
