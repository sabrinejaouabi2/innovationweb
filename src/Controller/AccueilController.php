<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Partenaire;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'app_accueil')]
    public function index(): Response
    {
        return $this->render('baseFront.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }
    #[Route('/part', name: 'app_part')]
    public function allpart(EntityManagerInterface $entityManager, Request $request, PartenaireRepository $PartenaireRepo, PaginatorInterface $paginator): Response  //PaginatorInterface $paginator
    {
        $part = $entityManager
            ->getRepository(Partenaire::class)
            ->findAll();

        $pagination = $paginator->paginate($part, $request->query->getInt('page', 1), 6);

        return $this->render('client/part.html.twig', [
            'parts' => $pagination,

        ]);
    }



}
