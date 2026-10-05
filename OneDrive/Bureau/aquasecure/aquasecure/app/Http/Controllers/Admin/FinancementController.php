<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FinancementRequest;
use App\Models\Financement;
use App\Models\ProjetRenovation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancementController extends Controller
{
    public function index(Request $request): View
    {
        $financements = Financement::query()
            ->when($request->filled('source'), fn ($q) => $q->where('source', $request->string('source')))
            ->when($request->filled('projet_id'), fn ($q) => $q->where('projet_id', $request->integer('projet_id')))
            ->with(['projetRenovation.reseauEau.zone'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $projets = ProjetRenovation::orderBy('titre')->get();

        $totalMontant = Financement::query()
            ->when($request->filled('source'), fn ($q) => $q->where('source', $request->string('source')))
            ->when($request->filled('projet_id'), fn ($q) => $q->where('projet_id', $request->integer('projet_id')))
            ->sum('montant');

        $stats = [
            'total_financements' => Financement::count(),
            'montant_global' => Financement::sum('montant'),
            'par_source' => Financement::selectRaw('source, count(*) as count, sum(montant) as total')
                ->groupBy('source')
                ->get()
                ->keyBy('source'),
        ];

        return view('back.financements.index', compact('financements', 'projets', 'totalMontant', 'stats'));
    }

    public function create(Request $request): View
    {
        $projets = ProjetRenovation::with('reseauEau')->orderBy('titre')->get();
        $financement = new Financement([
            'projet_id' => $request->integer('projet_id') ?: null,
            'source' => 'Etat',
        ]);

        return view('back.financements.create', compact('projets', 'financement'));
    }

    public function store(FinancementRequest $request): RedirectResponse
    {
        $financement = Financement::create($request->validated());

        if ($request->has('return_to_projet')) {
            return redirect()->route('admin.projets.show', $financement->projet_id)
                ->with('success', 'Financement de '.number_format($financement->montant, 2, ',', ' ').' TND ajouté avec succès.');
        }

        return redirect()->route('admin.financements.index')
            ->with('success', 'Financement de '.number_format($financement->montant, 2, ',', ' ').' TND enregistré avec succès.');
    }

    public function show(Financement $financement): View
    {
        $financement->load('projetRenovation.reseauEau.zone');

        return view('back.financements.show', compact('financement'));
    }

    public function edit(Financement $financement): View
    {
        $projets = ProjetRenovation::with('reseauEau')->orderBy('titre')->get();

        return view('back.financements.edit', compact('financement', 'projets'));
    }

    public function update(FinancementRequest $request, Financement $financement): RedirectResponse
    {
        $financement->update($request->validated());

        return redirect()->route('admin.financements.index')
            ->with('success', 'Financement mis à jour avec succès.');
    }

    public function destroy(Financement $financement): RedirectResponse
    {
        $projetId = $financement->projet_id;
        $montant = number_format($financement->montant, 2, ',', ' ');
        $financement->delete();

        return redirect()->back()
            ->with('success', 'Financement de '.$montant.' TND supprimé avec succès.');
    }
}
