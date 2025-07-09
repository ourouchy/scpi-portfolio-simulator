<?php
namespace App\Controller;

use App\Repository\ScpiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ScpiController extends AbstractController
{
    #[Route('/api/scpis', name: 'api_scpis_list', methods: ['GET'])]
    public function listScpis(ScpiRepository $scpiRepository): JsonResponse
    {
        $scpis = $scpiRepository->findAll();
        $data = array_map(function ($scpi) {
            return [
                'id' => $scpi->getId(),
                'nom' => $scpi->getNom(),
                'tauxRendementAnnuel' => $scpi->getTauxRendementAnnuel(),
            ];
        }, $scpis);
        return $this->json($data);
    }
}
