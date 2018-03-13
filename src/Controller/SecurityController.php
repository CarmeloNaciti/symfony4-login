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
use App\Service\EmailManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    public function login(Request $request, AuthenticationUtils $authUtils): Response
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
}