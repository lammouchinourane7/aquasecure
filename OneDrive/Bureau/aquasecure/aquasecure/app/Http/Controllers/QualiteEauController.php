<?php

namespace App\Http\Controllers;

use App\Models\AnalyseQualite;
use App\Models\PointPrelevement;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QualiteEauController extends Controller
{
    /**
     * Vue citoyenne (lecture seule) : qualité de l'eau aux points de
     * prélèvement actifs, d'après la dernière analyse de chacun.
     */
    public function index(Request $request): View
    {
        $points = PointPrelevement::query()
            ->where('actif', true)
            ->when($request->filled('zone_id'), fn ($q) => $q->whereHas('reseauEau', fn ($r) => $r->where('zone_id', $request->integer('zone_id'))))
            ->with(['reseauEau.zone', 'derniereAnalyse'])
            ->orderBy('nom')
            ->paginate(12)
            ->withQueryString();

        $zones = Zone::orderBy('nom')->get();

        $depuis = now()->subMonths(3);
        $totalRecentes = AnalyseQualite::where('date_prelevement', '>=', $depuis)->count();
        $resume = [
            'points' => PointPrelevement::where('actif', true)->count(),
            'analyses' => $totalRecentes,
            'taux' => $totalRecentes
                ? round(AnalyseQualite::where('date_prelevement', '>=', $depuis)->where('conforme', true)->count() / $totalRecentes * 100)
                : null,
        ];

        return view('front.qualite.index', compact('points', 'zones', 'resume'));
    }
}
