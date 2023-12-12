<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;


#[Route('/reclamayion')]
class ReclamayionController extends AbstractController
{
    #[Route("/displayjson", name: "app_Reclamation_displayjson1")]

    public function displayjson1(ReclamationRepository $repo, SerializerInterface $serializer)
    {
        $reclamation = $repo->findAll();

        $json = $serializer->serialize($reclamation, 'json', ['groups' => "reclamation"]);

        return new Response($json);
    }


    #[Route("/rec/{recid}", name: "one_rec")]
    public function reclamationId($recid, NormalizerInterface $normalizer, ReclamationRepository $repo )
    {
        $reclamation = $repo->find($recid);
        $reclamationNormalises = $normalizer->normalize($reclamation, 'json', ['groups' => "reclamation"]);
        return new Response(json_encode($reclamationNormalises));
    }



    #[Route("/addreclamationJSON", name: "addrecJSON")]
    public function addrecJSON(Request $req, NormalizerInterface $Normalizer,ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $reclamation = new Reclamation();
        $reclamation->setNom($req->get('nom'));
        $reclamation->setPrenom($req->get('prenom'));
        $reclamation->setTel($req->get('tel'));
        $reclamation->setMail($req->get('mail'));
        $reclamation->setRecldescription($req->get('recldescription'));
        $reclamation->setReclobject($req->get('reclobject'));
        $dateStr = $req->query->get('recldate');
        $recldate = DateTime::createFromFormat('Y-m-d', $dateStr);
        if (!$recldate instanceof DateTimeInterface) {
            return new JsonResponse(['error' => 'Invalid date format for date parameter.'], Response::HTTP_BAD_REQUEST);
        }
        $reclamation->setRecldate($recldate);

        $reclamation->setType($req->get('type'));

        $em->persist($reclamation);
        $em->flush();
        $jsonContent = $Normalizer->normalize($reclamation, 'json', ['groups' => 'reclamation']);
        return new Response(json_encode($jsonContent));
    }


    #[Route('/rec/nom/{nom}', name: 'reclamation_by_nom')]
    public function getReclamationByNom(string $nom, ReclamationRepository $reclamationRepository, NormalizerInterface $normalizer): Response
    {
        $reclamation = $reclamationRepository->findOneBy(['nom' => $nom]);

        if (!$reclamation) {
            throw $this->createNotFoundException('Reclamation not found');
        }

        $reclamationNormalized = $normalizer->normalize($reclamation, 'json', ['groups' => 'reclamation']);

        return new JsonResponse($reclamationNormalized);
    }




    #[Route("/updatejson/{recid}", name: "updatereclamationJSON")]
    public function updatereclamationJSON(Request $req, $recid, NormalizerInterface $Normalizer,ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $reclamation = $em->getRepository(Reclamation::class)->find($recid);
        //$reclamation->setNom($req->get('nom'));
        // $reclamation->setPrenom($req->get('prenom'));
        //$reclamation->setTel($req->get('tel'));
        $reclamation->setMail($req->get('mail'));
        $dateStr = $req->query->get('recldate');
        $recldate = DateTime::createFromFormat('Y-m-d', $dateStr);
        if (!$recldate instanceof DateTimeInterface) {
            return new JsonResponse(['error' => 'Invalid date format for date_validation_technique parameter.'], Response::HTTP_BAD_REQUEST);
        }
        $reclamation->setReclobject($req->get('reclobject'));
        $reclamation->setRecldescription($req->get('recldescription'));
        $reclamation->setType($req->get('type'));
        $em->flush();
        $jsonContent = $Normalizer->normalize($reclamation, 'json', ['groups' => 'reclamation']);
        return new Response("reclamation updated successfully " . json_encode($jsonContent));
    }

    #[Route("/deletejson/{recid}", name: "deletedestinationJSON")]
    public function deleteStudentJSON(Request $req, $recid, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $reclamation = $em->getRepository(Reclamation::class)->find($recid);
        $em->remove($reclamation);
        $em->flush();
        $jsonContent = $Normalizer->normalize($reclamation, 'json', ['groups' => 'reclamation']);
        return new Response("reclamation deleted successfully " . json_encode($jsonContent));
    }

    #[Route("/stat/type", name: "stattypeclfreeJSON")]
    public function statistiques()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT r.type, COUNT(r) as total FROM App\Entity\Reclamation r WHERE r.type IN (:types) GROUP BY r.type');
        $query->setParameter('types', array('client', 'freelance'));
        $results = $query->getResult();

        $statistiques = array();
        foreach ($results as $result) {
            $statistiques[$result['type']] = $result['total'];
        }

        return new JsonResponse($statistiques);
    }


    #[Route('/', name: 'app_reclamayion_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reclamations = $entityManager
            ->getRepository(Reclamation::class)
            ->findAll();

        return $this->render('reclamayion/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }





    #[Route('/new', name: 'app_reclamayion_new', methods: ['GET', 'POST'])]
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


            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamayion/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{recid}', name: 'app_reclamayion_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamayion/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{recid}/edit', name: 'app_reclamayion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamayion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamayion/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{recid}', name: 'app_reclamayion_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getRecid(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamayion_index', [], Response::HTTP_SEE_OTHER);
    }

    /*
        #[Route("/newjson", name: "addreclamationJSON")]
        public function addreclamationJSON(Request $req,  ManagerRegistry $doctrine,  NormalizerInterface $Normalizer)
        {

            $em = $doctrine->getManager();
            $reclamation = new Reclamation();
            $reclamation->setNom($req->get('nom'));
            $reclamation->setPrenom($req->get('prenom'));
            $reclamation->setTel($req->get('tel'));
            $reclamation->setMail($req->get('mail'));
            $date = $req->query->get('date');
            $date = DateTime::createFromFormat('Y-m-d', $date);
            $reclamation->setRecldate($date);
            $reclamation->setReclobject($req->get('reclobject'));
            $reclamation->setRecldescription($req->get('recldescription'));
            $reclamation->setType($req->get('type'));
            $em->persist($reclamation);
            $em->flush();
            $jsonContent = $Normalizer->normalize($reclamation, 'json', ['groups' => 'reclamation']);
            return new Response(json_encode($jsonContent));
        }
    */
}