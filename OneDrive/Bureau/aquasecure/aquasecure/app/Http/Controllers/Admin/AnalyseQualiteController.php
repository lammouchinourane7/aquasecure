<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AnalyseQualiteRequest;
use App\Models\AnalyseQualite;
use App\Models\PointPrelevement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyseQualiteController extends Controller
{
    public function index(Request $request): View
    {
        $analyses = AnalyseQualite::query()
            ->when($request->filled('point_id'), fn ($q) => $q->where('point_id', $request->integer('point_id')))
            ->when($request->input('conformite') === 'conforme', fn ($q) => $q->where('conforme', true))
            ->when($request->input('conformite') === 'non_conforme', fn ($q) => $q->where('conforme', false))
            ->when($request->filled('laboratoire'), fn ($q) => $q->where('laboratoire', $request->string('laboratoire')))
            ->when($request->filled('du'), fn ($q) => $q->whereDate('date_prelevement', '>=', $request->date('du')))
            ->when($request->filled('au'), fn ($q) => $q->whereDate('date_prelevement', '<=', $request->date('au')))
            ->with('point.reseauEau')
            ->orderByDesc('date_prelevement')
            ->paginate(15)
            ->withQueryString();

        $points = PointPrelevement::orderBy('code')->get();
        $laboratoires = AnalyseQualite::query()->distinct()->orderBy('laboratoire')->pluck('laboratoire');

        return view('back.analyses.index', compact('analyses', 'points', 'laboratoires'));
    }

    public function create(Request $request): View
    {
        $points = $this->pointsPourFormulaire();
        $analyse = new AnalyseQualite([
            'point_id' => $request->integer('point_id') ?: null,
            'date_prelevement' => now(),
            'bacteries_coliformes' => 0,
        ]);

        return view('back.analyses.create', compact('points', 'analyse'));
    }

    public function store(AnalyseQualiteRequest $request): RedirectResponse
    {
        $analyse = AnalyseQualite::create($request->validated());

        return redirect()
            ->route('admin.analyses.show', $analyse)
            ->with('success', $analyse->conforme
                ? 'Analyse enregistrée : eau conforme aux normes de potabilité.'
                : 'Analyse enregistrée — attention : eau NON conforme ('.implode(', ', $analyse->parametresNonConformes()).').');
    }

    public function show(AnalyseQualite $analyse): View
    {
        $analyse->load('point.reseauEau.zone');

        // Analyse précédente sur le même point, pour afficher l'évolution
        $precedente = AnalyseQualite::where('point_id', $analyse->point_id)
            ->where('date_prelevement', '<', $analyse->date_prelevement)
            ->orderByDesc('date_prelevement')
            ->first();

        return view('back.analyses.show', compact('analyse', 'precedente'));
    }

    public function edit(AnalyseQualite $analyse): View
    {
        $points = $this->pointsPourFormulaire();

        return view('back.analyses.edit', compact('analyse', 'points'));
    }

    public function update(AnalyseQualiteRequest $request, AnalyseQualite $analyse): RedirectResponse
    {
        $analyse->update($request->validated());

        return redirect()->route('admin.analyses.show', $analyse)->with('success', 'Analyse modifiée avec succès.');
    }

    public function destroy(AnalyseQualite $analyse): RedirectResponse
    {
        $pointId = $analyse->point_id;
        $analyse->delete();

        return redirect()->route('admin.points.show', $pointId)->with('success', 'Analyse supprimée avec succès.');
    }

    /**
     * Points proposés dans la liste déroulante, regroupés par réseau.
     */
    private function pointsPourFormulaire()
    {
        return PointPrelevement::with('reseauEau')->orderBy('code')->get()
            ->groupBy(fn (PointPrelevement $p) => $p->reseauEau?->type ?? 'Sans réseau');
    }
}
