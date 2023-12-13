<?php

namespace App\Controller;
use App\Entity\User;

use App\Entity\Evenement;
use App\Entity\Participation;
use App\Form\ParticipationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;










#[Route('/participation')]
class ParticipationController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    #[Route('/', name: 'app_participation_index', methods: ['GET'] )]
    public function index(EntityManagerInterface $entityManager,Request $request,PaginatorInterface $paginator): Response
    {
        $participations = $entityManager
            ->getRepository(Participation::class)
            ->findAll();
        $pagination = $paginator->paginate($participations, $request->query->getInt('page', 1), 7);

        return $this->render('participation/index.html.twig', [
           // 'participations' => $participations,
            'participations' => $pagination,
        ]);
    }



    #[Route('/mail/{eventid}', name: 'valider_part_freelance')]
    public function participer(MailerInterface $mailer, Evenement $event): Response
    {
        // Récupérer l'utilisateur statique une seule fois
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();

        // Récupérer l'utilisateur statique une seule fois
        $staticUser = $entityManager->getRepository(User::class)->find($user);
        // Vérifier le nombre de places disponibles pour l'événement
        $participantsCount = $entityManager->getRepository(Participation::class)->count(['eventid' => $event]);
         if ($participantsCount >5){
            // Le nombre maximum de participants a été atteint, afficher un message d'erreur
            return $this->render('participation/part_error.html.twig', [
                'event' => $event,
                'message' => "Le nombre maximum de participants pour cet événement a été atteint. Veuillez réessayer plus tard."
            ]);
        }

        $par = new Participation();
        $par->setEventid($event);
        $par->setId($staticUser);
        $par->setDatePart(new \DateTime());
$event->getEventtheme();


        $entityManager->persist($par);
        $entityManager->flush();
        $message = (new Email())
            ->from('sabrine.jaouabi@esprit.tn')
            ->to($staticUser->getEmail())
            ->subject('Confirmation de participation à un événement')
            ->html("
        <p style='font-size: 18px;'>Bonjour <span style='font-weight: bold; color:green;'></span>,</p>
      <p style='font-size: 16px; font-weight: bold;'>Votre demande de participation pour l'événement \"{$event->getEventtheme()}\" prévu le {$event->getDatedebutevent()->format('d/m/Y')} a été confirmée.</p>

        <p style='font-size: 14px;'>Merci de votre confiance.</p>
        <p style='font-size: 14px;'>Cordialement,</p>
        <p style='font-size: 14px;'>Equipe de freelanci</p>
    ");


        //->html($this->renderView('participation/part_success.html.twig', ['event' => $event]));
        // Send the email
        $result = $mailer->send($message);
      //  var_dump($result);

        return $this->render('participation/part_success.html.twig',[
            'event' => $event,

        ]);

    }



    #[Route('/mail/client/{eventid}', name: 'valider_part_client')]
    public function participerclient(Evenement $event): Response
    {
        // Récupérer l'utilisateur statique une seule fois
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();

        // Récupérer l'utilisateur statique une seule fois
        $staticUser = $entityManager->getRepository(User::class)->find($user);
        // Vérifier le nombre de places disponibles pour l'événement
        $participantsCount = $entityManager->getRepository(Participation::class)->count(['eventid' => $event]);
        if ($participantsCount > 5) {
            // Le nombre maximum de participants a été atteint, afficher un message d'erreur
            return $this->render('participation/part_error_client.html.twig', [
                'event' => $event,
                'message' => "Le nombre maximum de participants pour cet événement a été atteint. Veuillez réessayer plus tard."
            ]);
        }

        $par = new Participation();
        $par->setEventid($event);
        $par->setId($staticUser);
        $par->setDatePart(new \DateTime());
        $event->getEventtheme();

        $entityManager->persist($par);
        $entityManager->flush();

        return $this->render('participation/part_success_client.html.twig', [
            'event' => $event,
        ]);
    }


    #[Route('/new', name: 'app_participation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $participation = new Participation();

        //$currentDate = new \DateTime();
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participation);
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{idPart}', name: 'app_participation_show', methods: ['GET'])]
    public function show(Participation $participation): Response
    {
        return $this->render('participation/show.html.twig', [
            'participation' => $participation,
        ]);
    }

    #[Route('/{idPart}/edit', name: 'app_participation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{idPart}', name: 'app_participation_delete', methods: ['POST'])]
    public function delete(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getIdPart(), $request->request->get('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
    }



    //participation controller

    #[Route('/participation/{idPart}/edit', name: 'app_edit_participation')]
    public function editParticipation(Request $request, EntityManagerInterface $entityManager, int $idPart): Response
    {
        // Récupérer la participation à modifier
        $participation = $entityManager->getRepository(Participation::class)->find($idPart);

        if (!$participation) {
            // Si la participation n'existe pas, afficher une erreur
            throw $this->createNotFoundException('Participation introuvable');
        }

        // Récupérer le nouvel événement et le nouvel utilisateur à partir de la requête POST
        $nouvelEvenementId = $request->request->get('evenement');
        $nouvelUtilisateurId = $request->request->get('utilisateur');

        // Récupérer les instances des entités "Evenement" et "User" à partir de leur identifiant
        $nouvelEvenement = $entityManager->getRepository(Evenement::class)->find($nouvelEvenementId);
        $nouvelUtilisateur = $entityManager->getRepository(User::class)->find($nouvelUtilisateurId);

        // Mettre à jour les clés étrangères "id_evenment" et "id_user" de la participation
        $participation->setEventid($nouvelEvenement);
        $participation->setId($nouvelUtilisateur);

        // Enregistrer les modifications
        $entityManager->persist($participation);
        $entityManager->flush();

        // Rediriger vers la page de détails de l'événement
        return $this->redirectToRoute('app_event', ['eventid' => $participation->getEventid()->getId()]);
    }

}
