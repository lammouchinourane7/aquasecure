<?php

namespace App\Http\Controllers;

use App\Models\AnalyseQualite;
use App\Models\ProjetRenovation;
use App\Models\Releve;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Page d'accueil citoyenne : état du réseau par zone + résumé de chaque module.
     * Les gestionnaires et admins sont redirigés vers le back office.
     */
    public function index(): View|RedirectResponse
    {
        if (in_array(auth()->user()->role, ['gestionnaire', 'admin'], true)) {
            return redirect()->route('admin.dashboard');
        }

        // État de chaque tronçon du réseau, zone par zone
        $zones = Zone::with(['reseauEaus' => fn ($q) => $q->orderBy('type')])
            ->orderBy('commune')
            ->orderBy('nom')
            ->get();

        $reseaux = $zones->flatMap->reseauEaus;
        $etats = [
            'bon' => $reseaux->where('etat', 'bon')->count(),
            'degrade' => $reseaux->where('etat', 'degrade')->count(),
            'hors_service' => $reseaux->where('etat', 'hors_service')->count(),
        ];

        $depuis = now()->subMonths(3);
        $analysesRecentes = AnalyseQualite::where('date_prelevement', '>=', $depuis)->count();

        $resume = [
            'conformite' => $analysesRecentes
                ? round(AnalyseQualite::where('date_prelevement', '>=', $depuis)->where('conforme', true)->count() / $analysesRecentes * 100)
                : null,
            'derniere_analyse' => AnalyseQualite::max('date_prelevement'),
            'alertes' => Releve::where('hors_seuil', true)->where('date_releve', '>=', now()->subDays(7))->count(),
            'chantiers' => ProjetRenovation::where('statut', 'en_cours')->count(),
        ];

        return view('front.home', compact('zones', 'etats', 'resume'));
    }
}
