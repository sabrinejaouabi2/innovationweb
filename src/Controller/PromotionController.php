<?php

namespace App\Controller;

use App\Entity\Partenaire;
use App\Entity\Promotion;
use App\Form\PromotionType;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/promotion')]
class PromotionController extends AbstractController
{
    #[Route('/', name: 'app_promotion_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $promotions = $entityManager
            ->getRepository(Promotion::class)
            ->findAll();

        return $this->render('promotion/index.html.twig', [
            'promotions' => $promotions,
        ]);
    }

    #[Route('/new', name: 'app_promotion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $promotion = new Promotion();
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $entityManager->persist($promotion);
            $entityManager->flush();
            $this->addFlash ('success', 'Le promotion a été ajouté avec succès.');


            return $this->redirectToRoute('app_promotion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('promotion/new.html.twig', [
            'promotion' => $promotion,
            'form' => $form,
        ]);
    }

    #[Route('/{codepromo}', name: 'app_promotion_show', methods: ['GET'])]
    public function show(Promotion $promotion): Response
    {
        return $this->render('promotion/show.html.twig', [
            'promotion' => $promotion,
        ]);
    }

    #[Route('/detev/{codepromo}', name: 'app_detailspr_shows', methods: ['GET'])]
    public function showdetail(Promotion $promotion): Response
    {
        return $this->render('promotion/showDetail.html.twig', [
            'promotion' => $promotion,
        ]);
    }


    #[Route('/{codepromo}/edit', name: 'app_promotion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Promotion $promotion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash ('success', 'la promotion a été modifier avec succès.');


            return $this->redirectToRoute('app_promotion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('promotion/edit.html.twig', [
            'promotion' => $promotion,
            'form' => $form,
        ]);
    }

    #[Route('/{codepromo}', name: 'app_promotion_delete', methods: ['POST'])]
    public function delete(Request $request, Promotion $promotion, Partenaire $patenaire , PartenaireRepository $partenaireRepository,EntityManagerInterface $entityManager): Response
    {
        $partenaire = $partenaireRepository->findBy(['codepromo' => $promotion]);
        if (empty($partenaire)) {
            if ($this->isCsrfTokenValid('delete'.$promotion->getCodepromo(), $request->request->get('_token'))) {
                $entityManager->remove($promotion);
                $entityManager->flush();
            }
        } else {
            $this->addFlash('error', 'Veuillez supprimer le partenaire associée à cette promotion avant de la supprimer!');
        }
        return $this->redirectToRoute('app_promotion_index', [], Response::HTTP_SEE_OTHER);
}


}
