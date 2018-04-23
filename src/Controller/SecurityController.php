<?php
/**
 * Created by PhpStorm.
 * User: cn
 * Date: 2018/01/08
 * Time: 2:08 PM
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use App\Service\CacheManager;
use App\Service\EmailManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    public function login(AuthenticationUtils $authUtils): Response
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    public function logout(): Response
    {
        return $this->redirectToRoute('index');
    }

    public function forgotPassword(Request $request, EmailManager $email): Response
    {
        $form = $this->createForm(ForgotPasswordType::class, ['email' => $request->get('email')]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['email' => $data['email']]);

            if ($user) {
                if ($email->sendPasswordReset($data['email'])) {
                    $success = true;
                } else {
                    $error = 'There was an error sending the email';
                }
            } else {
                $error = 'No matching email address was found';
            }

        }

        return $this->render('security/forgot_password.html.twig', [
            'error' => $error ?? null,
            'form' => $form->createView(),
            'success' => $success ?? false,
        ]);
    }

    public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, CacheManager $cache, string $token, string $id): Response
    {
        $user = new User();

        try {
            $email = $cache->getEmailByToken($id, $token);
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['email' => $email]);
        } catch (\Exception $e) {
            // @todo - Handle Exception
        }

        $form = $this->createForm(ResetPasswordType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setIsActive(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render(
            'security/reset_password.html.twig',           [
                'form' => $form->createView()
            ]
        );
    }
    
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        # Get user from session
        $user = $this->get('security.token_storage')->getToken()->getUser(); 
        $form = $this->createForm(ChangePasswordType::class, $user);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            return $this->redirectToRoute('user_home');
        }
        return $this->render(
            'security/change_password.html.twig',           [
                'form' => $form->createView()
            ]
        );
    }
    
    public function editProfile(Request $request): Response
    {
        # Get user from session
        $user = $this->get('security.token_storage')->getToken()->getUser(); 
        $form = $this->createForm(ProfileType::class, $user);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            return $this->redirectToRoute('user_home');
        }
        return $this->render(
            'security/edit_profile.html.twig',           [
                'form' => $form->createView()
            ]
        );
    }
}