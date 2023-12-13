<?php

namespace App\Controller;

use App\Entity\Evenement;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Partenaire;
use App\Repository\PartenaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class DefaultController extends AbstractController
{

    #[Route('/home', name: 'home')]
    public function homePage(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }



    #[Route("/part/search", name: "part_search")]
    public function search(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {


        $searchQuery = $request->query->get('q');

        $parts = $this-> getDoctrine()
            ->getRepository(Partenaire::class)
            ->createQueryBuilder('p')
            ->leftJoin('p.codepromo', 'c')
            ->where('p.nompart LIKE :query')
            ->orWhere('c.nompromo LIKE :query')
            ->setParameter('query', '%' . $searchQuery . '%')
            ->getQuery()
            ->getResult();


        $pagination = $paginator->paginate($parts, $request->query->getInt('page', 1), 6);


        return $this->render('client/search_part.html.twig', [

            'parts' => $pagination,
            'search_query' => $searchQuery,

        ]);
    }


}


