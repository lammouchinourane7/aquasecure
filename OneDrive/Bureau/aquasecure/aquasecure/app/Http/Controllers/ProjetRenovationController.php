<?php

namespace App\Http\Controllers;

use App\Models\Financement;
use App\Models\ProjetRenovation;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjetRenovationController extends Controller
{
    /**
     * Vue citoyenne (lecture seule) : suivi des projets de rénovation
     * des infrastructures d'eau et de leurs financements.
     */
    public function index(Request $request): View
    {
        $projets = ProjetRenovation::query()
            ->when($request->filled('zone_id'), fn ($q) => $q->whereHas('reseauEau', fn ($r) => $r->where('zone_id', $request->integer('zone_id'))))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->string('statut')))
            ->when($request->filled('q'), fn ($q) => $q->where('titre', 'like', '%'.$request->string('q').'%'))
            ->with(['reseauEau.zone', 'financements'])
            ->withSum('financements', 'montant')
            ->latest()
            ->paginate(9)
            ->withQueryString();

        $zones = Zone::orderBy('nom')->get();

        $totalFinancements = Financement::sum('montant');
        $resume = [
            'total_projets' => ProjetRenovation::count(),
            'en_cours' => ProjetRenovation::where('statut', 'en_cours')->count(),
            'termines' => ProjetRenovation::where('statut', 'termine')->count(),
            'total_budget' => $totalFinancements,
        ];

        return view('front.projets.index', compact('projets', 'zones', 'resume'));
    }
}
