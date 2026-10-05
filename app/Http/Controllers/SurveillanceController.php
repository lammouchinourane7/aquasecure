<?php

namespace App\Http\Controllers;

use App\Models\Capteur;
use App\Models\Releve;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveillanceController extends Controller
{
    /**
     * Vue citoyenne (lecture seule) : état des capteurs installés sur les réseaux
     * et dernières valeurs mesurées.
     */
    public function index(Request $request): View
    {
        $capteurs = Capteur::query()
            ->when($request->filled('zone_id'), fn ($q) => $q->whereHas('reseauEau', fn ($r) => $r->where('zone_id', $request->integer('zone_id'))))
            ->when($request->filled('type_mesure'), fn ($q) => $q->where('type_mesure', $request->string('type_mesure')))
            ->with(['reseauEau.zone', 'dernierReleve'])
            ->orderBy('code')
            ->paginate(12)
            ->withQueryString();

        $zones = Zone::orderBy('nom')->get();

        $resume = [
            'actifs' => Capteur::where('statut', 'actif')->count(),
            'en_panne' => Capteur::where('statut', '!=', 'actif')->count(),
            'alertes_7j' => Releve::where('hors_seuil', true)->where('date_releve', '>=', now()->subDays(7))->count(),
        ];

        return view('front.surveillance.index', compact('capteurs', 'zones', 'resume'));
    }
}
