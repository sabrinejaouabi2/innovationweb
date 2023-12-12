<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserInteraction;
use App\Form\UserType;
use App\Repository\UserRepository;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
Use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

#[Route('/user')]
class UserController extends AbstractController
{

    #[Route("/ALLUsers", name: "listUser")]
    public function getUserss(UserRepository $repo, NormalizerInterface $normalizer)
    {
        $users = $repo->findAll();
        $usersNormalises  = $normalizer->normalize($users, 'json', ['groups' => "users"]);
        $json = json_encode($usersNormalises);
        return new Response($json);
    }


    #[Route("/Users/{id}", name: "User")]
    public function UserId($id, UserRepository $repo, NormalizerInterface $normalizer)
    {
        $users = $repo->find($id);
        $usersNormalises  = $normalizer->normalize($users, 'json', ['groups' => "users"]);
        $json = json_encode($usersNormalises);
        return new Response($json);
    }


    #[Route("/addUserJSON/new", name: "addUserJSON")]
    public function addUserJSON(Request $req, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setPrenomUser($req->get('prenom_user'));
        $user->setNomUser($req->get('nom_user'));
        $user->setEmail($req->get('email'));
        $hashedPassword = password_hash($req->get('password'), PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);
        $user->setAdresseUser($req->get('adresse_user'));
        $em->persist($user);
        $em->flush();

        return new JsonResponse("Acount is Created", 200);
    }


    #[Route("/updateUserJSON/{id}", name: "updateUserJSON")]
    public function updateUserJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $user->setNomUser($req->get('nom_user'));
        $user->setPrenomUser($req->get('prenom_user'));
        $user->setEmail($req->get('email'));
        $user->setAdresseUser($req->get('adresse_user'));
        $em->flush();
        $jsonContent = $Normalizer->normalize($user, 'json', ['groups' => 'users']);
        return new Response("User updated successfully " . json_encode($jsonContent));
    }

    #[Route("/deleteUserJSON/{id}", name: "deleteUserJSON")]
    public function deleteUserJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $em->remove($user);
        $em->flush();
        $jsonContent = $Normalizer->normalize($user, 'json', ['groups' => 'users']);
        return new Response("User deleted successfully " . json_encode($jsonContent));
    }

    #[Route("/LoginJSON", name: "addLoginJSON")]
    public function signInfunction (Request $req)
    {
        $email = $req->query->get("email");
        $password = $req->query->get("password");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user) {
            if (password_verify($password, $user->getPassword())) {
                $serializer = new Serializer([new ObjectNormalizer()]);
                $formatted = $serializer->normalize($user);
                return new JsonResponse($formatted);

            } else {
                return new Response ("password not found");
            }

        } else {

            return new Response ("user not found");

        }
    }

    ///**
    // * @Route("/loginJson" , name="loginn")
     ////*/
    //public function loginJson(Request $request, NormalizerInterface $normalizer)
    //{
        //$email = $request->query->get("email");
        //$password = $request->query->get("password");
        //$em = $this->getDoctrine()->getManager();
        //$user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
       // if ($user) {
            //if ($password  ==  $user->getPassword()) {

                //$jsonContent = $normalizer->normalize($user, 'json', ['groups' => 'users']);
              //  return new Response(json_encode($jsonContent));
            //} else {
           //     return new Response("password not found");
         //   }
       // }/ else {
       //     return new Response("user not found");
     //   }
   // }
//

    ///**
    // * @Route("/getPasswordByEmail", name="password")
    // */

    #[Route("/getPasswordByEmail", name: "getPasswordparEmail")]
    public function getPasswordByEmail(Request $request, NormalizerInterface $normalizer)
    {
        $email = $request->get('email');
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user) {
            $password = $user->getPassword();
            //$serializer = new Serialiser([new ObjectNormalizer()]);
            //$formatted =$serializer->normalize($password);
            //return new JsonResponse($formatted);
            $jsonContent = $normalizer->normalize($password, 'json', ['groups' => 'users']);
            return new Response(json_encode($jsonContent));
        }
        return new Response("user not found");
    }


    #[Route("/editProfileJson", name: "editProfil")]
    public function editUser1(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $id = $request->get("id");
        $username = $request->query->get("email");
        $password = $request->query->get("password");
        $prenom_user = $request->query->get("prenom_user");
        $nom_user = $request->query->get("nom_user");
        $adresse_user = $request->query->get("adresse_user");

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        $user->setEmail($username);
        $user->setPassword($request->get('password'));
        /*$user->setPassword(
            $encoder->encodePassword(
                $user,
                $password
            )
        );*/
        $user->setPrenomUser($prenom_user);
        $user->setNomUser($nom_user);
        $user->setAdresseUser($adresse_user);

        //$user->setIsVerified(true);
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return new JsonResponse("success", 200); //200 yaani http result ta3 server ok
        } catch (\Exception $ex) {
            return new Response("failed" . $ex->getMessage());
        }
    }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository,PaginatorInterface $paginator,Request $request): Response
    {
        $users= $userRepository->findAll();
        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1), 3
        );
        return $this->render('user/index.html.twig', [
            'pagination' =>$pagination,
            'users' => $users,

        ]);
    }



    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/user/search', name: 'user_search')]
    public function search(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $searchQuery = $request->query->get('q');

        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.nom_user LIKE :query')
            ->orWhere('u.prenom_user LIKE :query')
            ->setParameter('query', '%' . $searchQuery . '%')
            ->getQuery()
            ->getResult();

        $pagination = $paginator->paginate($users, $request->query->getInt('page', 1), 6);

        return $this->render('user/search.html.twig', [
            'users' => $pagination,
            'search_query' => $searchQuery,
        ]);
    }



    //block user by id
    #[Route('/block/{id}', name: 'app_user_block', methods: ['GET', 'POST'])]
    public function block(Request $request, User $user, UserRepository $userRepository): Response
    {
        $user->setIsBlocked(true);
        $user->setEtat(" bloque");
        $userRepository->save($user, true);
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    //unblock user by id
    #[Route('/unblock/{id}', name: 'app_user_unblock', methods: ['GET', 'POST'])]
    public function unblock(Request $request, User $user, UserRepository $userRepository): Response
    {
        $user->setIsBlocked(false);
        $user->setEtat("debloque");
        $userRepository->save($user, true);

        //mail
        $transport = new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls');
        $transport->setUsername('stickerstation434@gmail.com');
        $transport->setPassword('pwigfhxucyyryygt');

        // Create a new mailer instance with the transport object
        $mailer = new Swift_Mailer($transport);


        //BUNDLE MAILER
        $message = (new Swift_Message('Votre compte est débloqué'))
            ->setFrom('stickerstation434@gmail.com')
            ->setTo($user->getEmail())
            ->setBody
            ("<p> Cher utilisateur,<br/><br/><br/>

Nous sommes heureux de vous informer que votre compte a été débloqué sur notre site web.<br/> 
Nous espérons que vous pourrez maintenant profiter pleinement de notre plateforme et de toutes ses fonctionnalités.<br/>
Nous tenons à vous rappeler l'importance de respecter les règles de notre site web.<br/>
Ces règles sont en place pour assurer la sécurité et le respect mutuel entre tous les utilisateurs.<br/> 
Nous vous demandons donc de les suivre attentivement afin de garantir une expérience agréable pour tous.<br/><br/><br/>

Cordialement,<br/><br/><br/>

L'équipe administrative de notre site web.</p>",
                "text/html");


        //send mail
        $mailer->send($message);

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/handle_interaction/{id}", name="handle_interaction")
     */
    public function handleInteraction(User $interactedUser, Request $request, FlashBagInterface $flashBag): Response
    {
        $currentUser = $this->getUser();

        $interaction = $this->getDoctrine()
            ->getRepository(UserInteraction::class)
            ->findOneBy(['user' => $currentUser, 'interactedUser' => $interactedUser]);

        if (!$interaction) {
            $interaction = new UserInteraction();
            $interaction->setUser($currentUser);
            $interaction->setInteractedUser($interactedUser);
        }

        // Handle like/dislike based on user input
        $liked = $request->get('liked');
        $interaction->setLiked($liked);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($interaction);
        $entityManager->flush();

        // Set flash messages
        $message = $liked ? 'You liked the user!' : 'You disliked the user!';
        $flashType = $liked ? 'success' : 'danger';
        $flashBag->add($flashType, $message);

        return $this->redirectToRoute('all_users');
    }

}
