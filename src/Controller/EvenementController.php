<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Evenement;
use App\Entity\Favouris;
use App\Entity\Produit;
use App\Entity\User;
use App\Form\EvenmentType;
use App\Repository\EvenementRepository;
use App\Repository\favourisRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/evenement')]
class EvenementController extends AbstractController
{
    #[Route("/displayjsonevent", name: "app_event_displayjson")]
    public function displayjsonevent(EvenementRepository $repo, SerializerInterface $serializer)
    {
        $event = $repo->findAll();
        $json = $serializer->serialize($event, 'json', [
            'groups' => ['evenement']
        ]);
        return new Response($json);
    }

    #[Route("/addeventJSON", name: "addeventJSON")]
    public function addeventJSON(Request $req, NormalizerInterface $Normalizer,ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $event = new evenement();
        $event->setEventnom($req->get('eventnom'));
        $event->setEventtheme($req->get('eventtheme'));
        $event->setEventdescription($req->get('eventdescription'));
        $event->setEventadresse($req->get('eventadresse'));
        $event->setEventimage($req->get('eventimage'));
        $dateStr = $req->query->get('datedebutevent');
        $datedebutevent = DateTime::createFromFormat('Y-m-d', $dateStr);
        if (!$datedebutevent instanceof DateTimeInterface) {
            return new JsonResponse(['error' => 'Invalid date format for date parameter.'], Response::HTTP_BAD_REQUEST);
        }

        $event->setDatedebutevent($datedebutevent);

        //fin
        $dateStrfin = $req->query->get('datefinevent');
        $datefinevent = DateTime::createFromFormat('Y-m-d', $dateStrfin);
        if (!$datefinevent instanceof DateTimeInterface) {
            return new JsonResponse(['error' => 'Invalid date format for date parameter.'], Response::HTTP_BAD_REQUEST);
        }
        $event->setDatefinevent($datefinevent);


        $em->persist($event);
        $em->flush();
        $jsonContent = $Normalizer->normalize($event, 'json', ['groups' => 'evenement']);
        return new Response(json_encode($jsonContent));
    }

    #[Route("/updatejsonevent/{eventid}", name: "updateeventJSON")]
    public function updatejsonprod(Request $req, $eventid, NormalizerInterface $Normalizer,ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $event = $em->getRepository(Evenement::class)->find($eventid);
        $event = new evenement();
        $event->setEventnom($req->get('eventnom'));
        $event->setEventtheme($req->get('eventtheme'));
        $event->setEventdescription($req->get('eventdescription'));
        $event->setEventimage($req->get('eventimage'));
        $event->setEventadresse($req->get('eventadresse'));


        $em->flush();
        $jsonContent = $Normalizer->normalize($event, 'json', ['groups' => 'evenement']);
        return new Response("evenement updated successfully " .
            json_encode($jsonContent));
    }
    #[Route("/deletejsoneventJSON/{eventid}", name: "deleteeventJSON")]
    public function deletejsoneventJSON(Request $req, $eventid, NormalizerInterface $Normalizer,ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $event = $em->getRepository(Evenement::class)->find($eventid);
        $em->remove($event);
        $em->flush();
        $jsonContent = $Normalizer->normalize($event, 'json', ['groups' => 'evenement']);
        return new Response("evenment deleted successfully " .
            json_encode($jsonContent));
    }





























    #[Route('/', name: 'app_evenement_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager ,Request $request,PaginatorInterface $paginator): Response
    {
        $evenements = $entityManager
            ->getRepository(Evenement::class)
            ->findAll();
        $pagination = $paginator->paginate($evenements, $request->query->getInt('page', 1), 6);
        return $this->render('evenement/index.html.twig', [
            //  'evenements' => $evenements,
            'evenements'=> $pagination,
        ]);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenmentType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('eventimage')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception
                }

                $evenement->setEventimage($newFilename);
            }

            $entityManager->persist($evenement);
            $entityManager->flush();
            $this->addFlash ('success', 'L évenement a été crée avec succès.');

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{eventid}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/detev/{eventid}', name: 'app_detailsev_show', methods: ['GET'])]
    public function showdetail(Evenement $evenement): Response
    {
        return $this->render('evenement/showDetail.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{eventid}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenmentType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('eventimage')->getData();


            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception
                }

                $evenement->setEventimage($newFilename);
                //$evenement->setImage(new File($this->getParameter('images_directory').'/'.$newFilename));
            }

            $entityManager->flush();
            $this->addFlash ('success', 'L évenement a été modifier avec succès.');



            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{eventid}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager,favourisRepository $favourisRepository): Response
    {
        $favouris = $favourisRepository->findBy(['eventid' => $evenement]);

        if (empty($favouris)) {
            if ($this->isCsrfTokenValid('delete'.$evenement->getEventid(), $request->request->get('_token'))) {
                $entityManager->remove($evenement);
                $entityManager->flush();

                $this->addFlash('success', 'L evenement a été supprimé avec succès.');
            }
        } else {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer cet evenement car il existe dans les favoris!');
        }
        /*
                if ($this->isCsrfTokenValid('delete'.$evenement->getEventid(), $request->request->get('_token'))) {
                    $entityManager->remove($evenement);
                    $entityManager->flush();
                }
        */
        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/detevhistorique/{eventid}', name: 'app_detailhistorique_show', methods: ['GET'])]
    public function showdetailhistorique(Evenement $evenement): Response
    {
        return $this->render('evenement/showDetailhistorique.html.twig', [
            'evenement' => $evenement,
        ]);
    }



}