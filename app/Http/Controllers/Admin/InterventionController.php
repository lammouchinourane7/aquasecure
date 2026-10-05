<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InterventionRequest;
use App\Models\Incident;
use App\Models\Intervention;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InterventionController extends Controller
{
    public function index(Request $request): View
    {
        $interventions = Intervention::query()
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->string('statut')))
            ->when($request->filled('incident_id'), fn ($q) => $q->where('incident_id', $request->integer('incident_id')))
            ->when($request->filled('du'), fn ($q) => $q->whereDate('date_intervention', '>=', $request->date('du')))
            ->when($request->filled('au'), fn ($q) => $q->whereDate('date_intervention', '<=', $request->date('au')))
            ->with(['incident.reseauEau.zone'])
            ->orderByDesc('date_intervention')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $incidents = Incident::with('reseauEau')->orderByDesc('date_signalement')->get();

        return view('back.interventions.index', compact('interventions', 'incidents'));
    }

    public function create(Request $request): View
    {
        $incidents = Incident::with('reseauEau')->orderByDesc('date_signalement')->get();
        $intervention = new Intervention([
            'incident_id' => $request->integer('incident_id') ?: null,
            'statut' => 'planifiee',
            'date_intervention' => now()->toDateString(),
        ]);

        return view('back.interventions.create', compact('incidents', 'intervention'));
    }

    public function store(InterventionRequest $request): RedirectResponse
    {
        $intervention = Intervention::create($request->validated());

        return redirect()->route('admin.interventions.show', $intervention)->with('success', 'Intervention enregistrée avec succès.');
    }

    public function show(Intervention $intervention): View
    {
        $intervention->load(['incident.reseauEau.zone']);

        return view('back.interventions.show', compact('intervention'));
    }

    public function edit(Intervention $intervention): View
    {
        $incidents = Incident::with('reseauEau')->orderByDesc('date_signalement')->get();

        return view('back.interventions.edit', compact('intervention', 'incidents'));
    }

    public function update(InterventionRequest $request, Intervention $intervention): RedirectResponse
    {
        $intervention->update($request->validated());

        return redirect()->route('admin.interventions.show', $intervention)->with('success', 'Intervention modifiée avec succès.');
    }

    public function destroy(Intervention $intervention): RedirectResponse
    {
        $intervention->delete();

        return redirect()->route('admin.interventions.index')->with('success', 'Intervention supprimée avec succès.');
    }
}
