<?php

namespace App\Controller;

use Twilio\Rest\Client;
use App\Entity\Partenaire;
use App\Form\PartenaireType;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#[Route('/partenaire')]
class PartenaireController extends AbstractController
{

    #[Route('/', name: 'app_partenaire_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $partenaires = $entityManager
            ->getRepository(Partenaire::class)
            ->findAll();
        $nbvueCounts = $entityManager->createQueryBuilder()
            ->select('p.nompart', 'SUM(p.nbvue) AS nbvueSum')
            ->from(Partenaire::class, 'p')
            ->groupBy('p.nompart')
            ->getQuery()
            ->getResult();

        return $this->render('partenaire/index.html.twig', [
            'partenaires' => $partenaires,
            'nbvueCounts' => $nbvueCounts,

        ]);
    }

    #[Route('/new', name: 'app_partenaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $session = $request->getSession();
        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            $image = $form->get('logopart')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception
                }

                $partenaire->setLogopart($newFilename);
                $partenaire->setCodePromo($form->get('codepromo')->getData());
            }


            var_dump($session->getFlashBag()->get('success'));

            $entityManager->persist($partenaire);
            $entityManager->flush();
            //Ajouter un message flash
            $this->addFlash ('success', 'le partenaire a été ajouté avec succès.');

            return $this->redirectToRoute('app_partenaire_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('partenaire/new.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form,
        ]);
    }

    #[Route('/{idpart}', name: 'app_partenaire_show', methods: ['GET'])]
    public function show(Partenaire $partenaire): Response
    {
        return $this->render('partenaire/show.html.twig', [
            'partenaire' => $partenaire,
        ]);
    }

    #[Route('/detev/{idpart}', name: 'app_detailspa_shows', methods: ['GET'])]
    public function showdetail(Partenaire $partenaire): Response
    {
        return $this->render('partenaire/showDetail.html.twig', [
            'partenaire' => $partenaire,
        ]);
    }


    #[Route('/{idpart}/edit', name: 'app_partenaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Partenaire $partenaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $image = $form->get('logopart')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception
                }

                $partenaire->setLogopart($newFilename);

            }
            $partenaire->setCodePromo($form->get('codepromo')->getData());

            $entityManager->flush();
            $this->addFlash ('success', 'le partenair a été modifié avec succès.');


            return $this->redirectToRoute('app_partenaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('partenaire/edit.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form,
        ]);
    }

    #[Route('/{idpart}', name: 'app_partenaire_delete', methods: ['POST'])]
    public function delete(Request $request, Partenaire $partenaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $partenaire->getIdpart(), $request->request->get('_token'))) {
            $entityManager->remove($partenaire);
            $entityManager->flush();
            $this->addFlash ('success', 'le partenair a été Supprimeé avec succès.');


        }

        return $this->redirectToRoute('app_partenaire_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/partenaire/delete-multiple", name="app_partenaire_delete_multiple", methods={"POST"})
     */
    public function deleteMultiple(Request $request, EntityManagerInterface $em)
    {
        $ids = $request->request->get('partenaire');
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $partenaire = $em->getRepository(Partenaire::class)->find($id);
                if ($partenaire) {
                    $em->remove($partenaire);
                }
            }
            $em->flush();
            $this->addFlash('success', 'Les partenaires ont été supprimés avec succès.');
        }
        return $this->redirectToRoute('app_partenaire_index');
    }

    #[Route('/partenaire/{idpart}/nbvue', name: 'app_partenaire_nbvue', methods: ['POST'])]
    public function nbvue(Request $request, EntityManagerInterface $entityManager, Partenaire $partenaire, $idpart): Response
    {
        $partenaire = $entityManager
            ->getRepository(Partenaire::class)
            ->find($idpart);
        if (!$partenaire) {
            throw $this->createNotFoundException(
                'No partner found for id ' . $idpart
            );
        }

        $partenaire->setNbvue($partenaire->getNbvue() + 1);
        $entityManager->flush();

// Redirect to the index page after liking the annonce
        return new RedirectResponse($this->generateUrl('app_part'));
    }



    #[Route('/statvue', name: 'app_statvue')]
    public function index2(PartenaireRepository $partenaireRepository, EntityManagerInterface $entityManager): Response
    {


        $nbvueCounts = $entityManager->createQueryBuilder()
            ->select('p.nompart', 'SUM(p.nbvue) AS nbvueSum')
            ->from(Partenaire::class, 'p')
            ->groupBy('p.nompart')
            ->getQuery()
            ->getResult();

        return $this->render('partenaire/index.html.twig', [
            'nbvueCounts' => $nbvueCounts,


        ]);


    }
}



