<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjetRenovationRequest;
use App\Models\ProjetRenovation;
use App\Models\ReseauEau;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjetRenovationController extends Controller
{
    public function index(Request $request): View
    {
        $projets = ProjetRenovation::query()
            ->when($request->filled('q'), fn ($q) => $q->where('titre', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->string('statut')))
            ->when($request->filled('reseau_id'), fn ($q) => $q->where('reseau_id', $request->integer('reseau_id')))
            ->with(['reseauEau.zone'])
            ->withCount('financements')
            ->withSum('financements', 'montant')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $reseaux = ReseauEau::with('zone')->orderBy('type')->get();

        $stats = [
            'total' => ProjetRenovation::count(),
            'planifie' => ProjetRenovation::where('statut', 'planifie')->count(),
            'en_cours' => ProjetRenovation::where('statut', 'en_cours')->count(),
            'termine' => ProjetRenovation::where('statut', 'termine')->count(),
        ];

        return view('back.projets.index', compact('projets', 'reseaux', 'stats'));
    }

    public function create(Request $request): View
    {
        $reseaux = ReseauEau::with('zone')->orderBy('type')->get();
        $projet = new ProjetRenovation([
            'reseau_id' => $request->integer('reseau_id') ?: null,
            'statut' => 'planifie',
        ]);

        return view('back.projets.create', compact('reseaux', 'projet'));
    }

    public function store(ProjetRenovationRequest $request): RedirectResponse
    {
        $projet = ProjetRenovation::create($request->validated());

        return redirect()->route('admin.projets.show', $projet)
            ->with('success', 'Projet de rénovation « '.$projet->titre.' » créé avec succès.');
    }

    public function show(ProjetRenovation $projet): View
    {
        $projet->load(['reseauEau.zone', 'financements' => fn ($q) => $q->latest()]);
        $totalMontant = $projet->financements->sum('montant');

        $repartitionParSource = $projet->financements
            ->groupBy('source')
            ->map(fn ($group, $source) => [
                'source' => $source,
                'label' => \App\Models\Financement::SOURCES[$source] ?? $source,
                'total' => $group->sum('montant'),
                'count' => $group->count(),
                'pourcentage' => $totalMontant > 0 ? round(($group->sum('montant') / $totalMontant) * 100, 1) : 0,
            ])
            ->values();

        return view('back.projets.show', compact('projet', 'totalMontant', 'repartitionParSource'));
    }

    public function edit(ProjetRenovation $projet): View
    {
        $reseaux = ReseauEau::with('zone')->orderBy('type')->get();

        return view('back.projets.edit', compact('projet', 'reseaux'));
    }

    public function update(ProjetRenovationRequest $request, ProjetRenovation $projet): RedirectResponse
    {
        $projet->update($request->validated());

        return redirect()->route('admin.projets.show', $projet)
            ->with('success', 'Projet « '.$projet->titre.' » mis à jour avec succès.');
    }

    public function destroy(ProjetRenovation $projet): RedirectResponse
    {
        $titre = $projet->titre;
        $projet->delete();

        return redirect()->route('admin.projets.index')
            ->with('success', 'Projet « '.$titre.' » et ses financements associés supprimés avec succès.');
    }
}
