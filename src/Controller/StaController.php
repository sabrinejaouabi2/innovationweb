<?php

namespace App\Controller;

use App\Entity\Evenement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class StaController extends AbstractController
{
    #[Route('/statistiques', name: 'api_statistiques')]
    public function statistiques()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT r.type, COUNT(r) as total FROM App\Entity\Reclamation r WHERE r.type IN (:types) GROUP BY r.type');
        $query->setParameter('types', array('client'));
        $results = $query->getResult();

        $statistiques = array();
        foreach ($results as $result) {
            $statistiques[$result['type']] = $result['total'];
        }

       // return new JsonResponse($statistiques);
        return $this->render('sta/index.html.twig', [
            'statistiques' => $statistiques,
        ]);
    }


    #[Route('/statistique/favoris', name: 'api_stat_favoris')]
    public function statistiquesFavoris()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT e.eventnom, (
        SELECT COUNT(f.eventid) FROM App\Entity\Favouris f WHERE f.eventid = e.eventid
    ) as total_favouris
    FROM App\Entity\Evenement e');

        $results = $query->getResult();

        $statistiques = array();
        foreach ($results as $result) {
            $statistiques[$result['eventnom']] = $result['total_favouris'];
        }

        // do something with $statistiques
        return $this->render('sta/favouris.html.twig', [
            'statistiques' => $statistiques,
        ]);
    }




    #[Route('/statistique/participations', name: 'api_stat_participations')]
    public function statistiqueseventsansParticipations()
    {
        $entityManager = $this->getDoctrine()->getManager();

/*
        $query = $entityManager->createQuery('SELECT COUNT(e.eventid) as nb_evenements_sans_participants
    FROM App\Entity\Evenement e
    WHERE NOT EXISTS (
        SELECT p 
        FROM App\Entity\Participation p 
        WHERE p.eventid = e.eventid
    )');



        $nb_evenements_sans_participants = $query->getSingleScalarResult();

*/
        $query = $entityManager->createQuery('SELECT e.eventtheme ,COUNT(e.eventid) as nb_evenements_sans_participants
    FROM App\Entity\Evenement e
    WHERE NOT EXISTS (
        SELECT p 
        FROM App\Entity\Participation p 
        WHERE p.eventid = e.eventid
    )');

        $eventThemes = $query->getResult();


        return $this->render('sta/detail.html.twig', [


            'eventThemes' => $eventThemes,
        ]);

    }






}



