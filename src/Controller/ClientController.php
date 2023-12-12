<?php

namespace App\Controller;
use App\Entity\Partenaire;
use App\Entity\Reclamation;
use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Favouris;
use App\Entity\Evenement;
use App\Form\ReclamationType;
use App\Form\SearchProduitType;
use App\Repository\CategorieRepository;
use App\Repository\EvenementRepository;
use App\Repository\PartenaireRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client')]
    public function index(): Response
    {
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }
    #[Route('/rec', name: 'recc')]
    public function recPage(): Response
    {
        return $this->render('reclamayion/new.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }
    #[Route('/part_client', name: 'app_part_Client')]
    public function allpart(EntityManagerInterface $entityManager, Request $request, PartenaireRepository $PartenaireRepo, PaginatorInterface $paginator): Response  //PaginatorInterface $paginator
    {
        $part = $entityManager
            ->getRepository(Partenaire::class)
            ->findAll();

        $pagination = $paginator->paginate($part, $request->query->getInt('page', 1), 6);

        return $this->render('client/partClient.html.twig', [
            'parts' => $pagination,

        ]);
    }


    #[Route('/add-to-favorites/{eventid}', name:'add_to_favorites')]
    public function addToFavorites($eventid,SessionInterface $session)
    {
        $entityManager = $this->getDoctrine()->getManager();

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        // Récupérer l'événement à ajouter aux favoris
        $event = $entityManager->getRepository(Evenement::class)->find($eventid);
        // Vérifier si l'utilisateur a déjà ajouté cet événement aux favoris
        $existingFavorite = $entityManager->getRepository(Favouris::class)->findOneBy(['id' => $user, 'eventid' => $event]);

        if ($existingFavorite != null) {
            $session->getFlashBag()->add('error', 'L\'événement est déjà dans vos favoris !');
            $session->save();
            $notification = '<div class="alert alert-danger" role="alert">L\'événement est déjà dans vos favoris !</div>';
            // Define notification variable
            return $this->redirectToRoute('app_event', ['eventid' => $eventid]);
        }
        // Créer un nouvel objet Favourites
        $favourites = new Favouris();

        $favourites->setId($user);
        $favourites->setEventid($event);

        // Enregistrer l'objet dans la base de données
        $entityManager->persist($favourites);
        $entityManager->flush();
        // Ajouter un message flash à la session de l'utilisateur
        $session->getFlashBag()->add('success', 'L\'événement a été ajouté aux favoris avec succès !');
        // Define notification variable
        $notification = '<div class="alert alert-success" role="alert">L\'événement a été ajouté aux favoris avec succès !</div>';
        // Rediriger l'utilisateur vers la page de l'événement
        //return $this->redirectToRoute('app_detailsev_show', ['eventid' => $eventid]);
        return $this->redirectToRoute('app_event', ['eventid' => $eventid]);
    }

    #[Route('/new/clienttrecc', name: 'app_reclamayionrec_new_client', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();
            // Ajouter un message de succès
            $this->addFlash('success', 'Votre réclamation a été soumise avec succès !');


            return $this->redirectToRoute('app_client', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamayion/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }
//liste de vos favouris

    #[Route('/favorite/client', name: 'myfavorites')]
    public function myFavorites(Request $request, PaginatorInterface $paginator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $evenements = $entityManager->getRepository(Evenement::class)->findAll();
        // Récupérer les favoris de l'utilisateur avec une jointure entre la table des utilisateurs et la table des favoris
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->createQueryBuilder()
            ->select('e')
            ->from('App\Entity\Evenement', 'e')
            ->innerJoin('App\Entity\Favouris', 'f', 'WITH', 'f.eventid = e.eventid')
            ->where('f.id = :id')
            ->setParameter('id', $this->getUser()->getId()) // id de l'utilisateur connecté
           // ->setParameter('id', 40) // id de l'utilisateur
            ->getQuery();

        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 3);

        // Afficher la liste des favoris
        if(count($pagination) > 0) {
            dump('La requête a renvoyé des résultats:');
            dump($pagination);
        } else {
            dump('La requête n\'a pas renvoyé de résultats');
        }

        // Afficher la liste des favoris
        return $this->render('favouris/favorites.html.twig', [
            'pagination' => $pagination,
        ]);
    }




    #[Route('/favorite/delete/{eventid}', name: 'delete_favorite')]
    public function deleteFavorite(Request $request, int $eventid, EntityManagerInterface $entityManager): Response
    {
         $user = $this->getUser(); // Récupérer l'utilisateur courant
       // $user = $entityManager->getRepository(User::class)->find(40); // Récupérer l'utilisateur avec l'ID 40


        // Trouver l'entrée correspondante dans la table des favoris
        $favorite = $entityManager->getRepository(Favouris::class)->findOneBy([
            'eventid' => $eventid,
            'id' => $user->getId(),
        ]);

        // Vérifier si l'entrée a été trouvée
        if (!$favorite) {
            throw $this->createNotFoundException('Favori non trouvé');
        }

        // Supprimer l'entrée de la table des favoris
        $entityManager->remove($favorite);
        $entityManager->flush();

        $this->addFlash('success', 'Le favori a été supprimé avec succès.');

        return $this->redirectToRoute('myfavorites');
    }



    #[Route('/eventclient', name: 'app_event')]
    public function allevent(EntityManagerInterface $entityManager, Request $request, EvenementRepository $EvenementRepo, PaginatorInterface $paginator): Response  //PaginatorInterface $paginator
    {
        $today = new \DateTime(); // Get today's date

        $query = $entityManager->createQueryBuilder()
            ->select('e')
            ->from(Evenement::class, 'e')
            ->where('e.datedebutevent >= :today') // Filter events by start date
            ->setParameter('today', $today)
            ->getQuery();

        $event = $query->getResult();
        $pagination = $paginator->paginate($event, $request->query->getInt('page', 1), 6);
        return $this->render('client/event.html.twig', [
            'events' => $pagination,
            // 'favorites' => $favorites,

        ]);
    }



    #[Route("/event/search", name: "event_searchclient")]
    public function searcheventclient(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $searchQuery = $request->query->get('q');
        $events = $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->createQueryBuilder('e')
            ->where('e.eventnom LIKE :query')
            ->orWhere('e.eventtheme LIKE :query')
            ->setParameter('query', '%' . $searchQuery . '%')
            ->getQuery()
            ->getResult();


        $pagination = $paginator->paginate($events, $request->query->getInt('page', 1), 6);

        return $this->render('client/search.html.twig', [

            'events' => $pagination,
            'search_query' => $searchQuery,

        ]);
    }

    #[Route("/event/filter_v", name: "event_filter")]
    public function alleventfilterclient(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        // récupérer les paramètres de requête pour filtrer les événements
        $startDate = $request->query->get('datedebutevent');
        try {
            if ($startDate && !\DateTime::createFromFormat('Y-m-d', $startDate)) {
                throw new \InvalidArgumentException('Invalid start date format');
            }
        } catch (\InvalidArgumentException $e) {
            // Afficher un message d'erreur personnalisé
            $this->addFlash('error', 'Dates saisies non valides. Veuillez saisir des dates valides au format YYYY-MM-DD.');
            return $this->redirectToRoute('event_filter');
        }

        // récupérer les événements correspondants aux critères de filtrage
        $eventRepo = $entityManager->getRepository(Evenement::class);
        $queryBuilder = $eventRepo->createQueryBuilder('e');
        if ($startDate) {
            $queryBuilder->andWhere('e.datedebutevent >= :datedebutevent')
                ->setParameter('datedebutevent', $startDate);
        }
        $query = $queryBuilder->getQuery();
        $event = $paginator->paginate($query, $request->query->getInt('page', 1), 6);


        return $this->render('client/event.html.twig', [
            'events' => $event,
        ]);
    }


    #[Route('/historique', name: 'app_historique')]
    public function historiqueevent(EntityManagerInterface $entityManager, Request $request, EvenementRepository $EvenementRepo, PaginatorInterface $paginator): Response  //PaginatorInterface $paginator
    {
        $today = new \DateTime(); // Get today's date
        $query = $entityManager->createQueryBuilder()
            ->select('e')
            ->from(Evenement::class, 'e')
            ->where('e.datefinevent < :today') // Filter events by end date
            ->setParameter('today', $today)
            ->getQuery();

        $event = $query->getResult();
        $pagination = $paginator->paginate($event, $request->query->getInt('page', 1), 6);

        return $this->render('client/historique.html.twig', [
            'events' => $pagination,

        ]);
    }


    #[Route('/produit', name: 'app_prod')]
    public function allproduit(EntityManagerInterface $entityManager, Request $request, ProduitRepository $ProduitRepo, CategorieRepository $catrepo ): Response
    {   //Recuperer les filters
        $filters=$request->get("categorie");
        //recuperer les produits sans pagination
        $produits=$ProduitRepo->getProduit($filters);

        // On vérifie si on a une requête Ajax
        if($request->get('ajax')){
            return new JsonResponse([
                'content' => $this->renderView('client/content_front.html.twig', compact('produits'))
            ]);
        }
        $form=$this->createForm(SearchProduitType::class);
        $search = $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //on recherche les produit correspondant aux mots
            $produits=$ProduitRepo->search($search->get('mots')->getData());
        }

        // Récupérer toutes les catégories
        $categories = $catrepo->findAll();

        return $this->render('client/produit.html.twig', compact('produits', 'categories', 'form'));

    }

}

