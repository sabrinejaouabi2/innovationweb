<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use App\Form\ForgotPWType;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class SecurityController extends AbstractController
{

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/forgot', name: 'forgot')]
    public function forgotPassword(Request $request, UserRepository $userRepository,Swift_Mailer $mailer, TokenGeneratorInterface  $tokenGenerator)
    {


        $form = $this->createForm(ForgotPWType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData();//


            $user = $userRepository->findOneBy(['email'=>$donnees]);
            if(!$user) {
                $this->addFlash('danger','Cette adresse n\'existe pas');
                return $this->redirectToRoute("forgot");

            }
            $token = $tokenGenerator->generateToken();

            try{
                //              $user->setResetToken($token);
                $entityManger = $this->getDoctrine()->getManager();
                $entityManger->persist($user);
                $entityManger->flush();




            }catch(\Exception $exception) {
                $this->addFlash('warning','une erreur est survenue :'.$exception->getMessage());
                return $this->redirectToRoute("app_login");


            }

            $url = $this->generateUrl('app_reset_password',array('token'=>$token),UrlGeneratorInterface::ABSOLUTE_URL);            // Create a new SMTP transport with the desired configuration
            $transport = new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls');
            $transport->setUsername('stickerstation434@gmail.com');
            $transport->setPassword('pwigfhxucyyryygt');

            // Create a new mailer instance with the transport object
            $mailer = new Swift_Mailer($transport);


            //BUNDLE MAILER
            $message = (new Swift_Message('Mot de passe oublié'))
                ->setFrom('stickerstation434@gmail.com')
                ->setTo($user->getEmail())
                ->setBody
                ("<p> Salut,<br/>
                            une demande de réinitialisation de mot de passe a été effectuée. 
                            Veuillez cliquer sur le lien suivant :</p>".$url,
                    "text/html");


            //send mail
            $mailer->send($message);
            $this->addFlash('message','E-mail  de réinitialisation du mot de passe est envoyé :');
            //return $this->redirectToRoute("app_reset_password", ['token' => $token]);



        }

        return $this->render("security/oublieMDP.html.twig",['form'=>$form->createView()]);
    }

    /**
     * @Route("/reset-password/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request,string $token, UserPasswordEncoderInterface  $passwordEncoder)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token'=>$token]);

        if($user == null ) {
            $this->addFlash('danger','TOKEN INCONNU');
            return $this->redirectToRoute("app_login");

        }

        if($request->isMethod('POST')) {
            $user->setResetToken(null);

            $user->setMdp($passwordEncoder->encodePassword($user,$request->request->get('password')));
            $entityManger = $this->getDoctrine()->getManager();
            $entityManger->persist($user);
            $entityManger->flush();

            $this->addFlash('message','Mot de passe mis à jour :');
            return $this->redirectToRoute("app_login");

        }

        return $this->render("security/resetPassword.html.twig",['token'=>$token]);
    }



    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(Request $request): Response
    {
        $this->get('security.token_storage')->setToken(null);
        $request->getSession()->invalidate();

        $response = $this->redirectToRoute('app_login');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;    }
}
