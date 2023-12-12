<?php

namespace App\Controller;



use App\Entity\Reclamation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;

class PdfController extends AbstractController
{
    #[Route('/pdf', name: 'app_pdf')]
    public function generatePdf(EntityManagerInterface $entityManager): Response
    {
        $reclamations = $entityManager
            ->getRepository(Reclamation::class)
            ->findAll();

        // Récupérez le contenu HTML de votre template avec le tableau
        $html = $this->renderView('pdf/index.html.twig', [
            'reclamations' => $reclamations,
        ]);

        // Créez un objet Dompdf et ajoutez le contenu HTML
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);



        // Rendez le document PDF
        $dompdf->render();

        // Enregistrez le fichier PDF généré
        $pdfContent = $dompdf->output();
        file_put_contents('document.pdf', $pdfContent);

        // Affichez le document PDF généré dans le navigateur
        return new Response($pdfContent, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"'
        ]);
    }
}

