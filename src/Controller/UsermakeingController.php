<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response; // Ajout de l'importation
use Symfony\Component\HttpFoundation\Request; // Ajout de l'importation
use Symfony\Component\Routing\Annotation\Route; // Ajout de l'importation
use App\Entity\User;
use App\Entity\UserInteraction;



class UsermakeingController extends AbstractController
{

    /**
     * @Route("/users", name="all_users")
     */
    public function showAllUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('user/showinteraction.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user-likes", name="user_likes")
     */
    public function userLikes(): Response
    {
        // Récupérez l'utilisateur connecté
        $user = $this->getUser();

        // Récupérez les likes de l'utilisateur (vous devrez adapter cela en fonction de votre modèle de données)
        $likes = $this->getDoctrine()->getRepository(UserInteraction::class)->findBy(['user' => $user, 'liked' => true]);

        return $this->render('user/user_likes.html.twig', [
            'likes' => $likes,
        ]);
    }



}