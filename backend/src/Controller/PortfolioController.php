<?php
namespace App\Controller;

use App\Repository\ScpiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PortfolioController extends AbstractController
{
    #[Route('/api/portfolio', name: 'api_portfolio_simulate', methods: ['POST'])]
    public function simulatePortfolio(Request $request, ScpiRepository $scpiRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $portefeuille = $data['portefeuille'] ?? null;
        if (!$portefeuille || !is_array($portefeuille) || count($portefeuille) === 0) {
            return $this->json(['error' => 'Portefeuille invalide'], 400);
        }

        $montantTotal = 0;
        $rendementPondere = 0;
        $revenuAnnuel = 0;
        $details = [];

        foreach ($portefeuille as $entry) {
            $scpiId = $entry['scpiId'] ?? null;
            $montant = $entry['montant'] ?? null;
            if (!$scpiId || !$montant || $montant < 0) {
                return $this->json(['error' => 'Entrée portefeuille invalide'], 400);
            }
            $scpi = $scpiRepository->find($scpiId);
            if (!$scpi) {
                return $this->json(['error' => "SCPI id $scpiId non trouvée"], 404);
            }
            $montantTotal += $montant;
            $rendement = $scpi->getTauxRendementAnnuel();
            $rendementPondere += $montant * $rendement;
            $revenu = $montant * $rendement / 100;
            $revenuAnnuel += $revenu;
            $details[] = [
                'scpiId' => $scpiId,
                'montant' => $montant,
                'rendement' => $rendement,
                'revenuAnnuel' => round($revenu, 2),
            ];
        }

        $rendementMoyen = $montantTotal > 0 ? $rendementPondere / $montantTotal : 0;
        $revenuMensuel = $revenuAnnuel / 12;

        return $this->json([
            'montantTotal' => $montantTotal,
            'rendementMoyen' => round($rendementMoyen, 2),
            'revenuAnnuel' => round($revenuAnnuel, 2),
            'revenuMensuel' => round($revenuMensuel, 2),
            'details' => $details,
        ]);
    }
}
