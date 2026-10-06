<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IncidentRequest;
use App\Models\Incident;
use App\Models\ReseauEau;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncidentController extends Controller
{
    public function index(Request $request): View
    {
        $incidents = Incident::query()
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->when($request->filled('gravite'), fn ($q) => $q->where('gravite', $request->string('gravite')))
            ->when($request->filled('reseau_id'), fn ($q) => $q->where('reseau_id', $request->integer('reseau_id')))
            ->when($request->filled('du'), fn ($q) => $q->whereDate('date_signalement', '>=', $request->date('du')))
            ->when($request->filled('au'), fn ($q) => $q->whereDate('date_signalement', '<=', $request->date('au')))
            ->with(['reseauEau.zone'])
            ->withCount('interventions')
            ->orderByDesc('date_signalement')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $reseaux = ReseauEau::with('zone')->orderBy('type')->get();

        return view('back.incidents.index', compact('incidents', 'reseaux'));
    }

    public function create(Request $request): View
    {
        $reseaux = ReseauEau::with('zone')->orderBy('type')->get();
        $incident = new Incident([
            'reseau_id' => $request->integer('reseau_id') ?: null,
            'type' => 'fuite',
            'gravite' => 'moyenne',
            'date_signalement' => now()->toDateString(),
        ]);

        return view('back.incidents.create', compact('reseaux', 'incident'));
    }

    public function store(IncidentRequest $request): RedirectResponse
    {
        $incident = Incident::create($request->validated());

        return redirect()->route('admin.incidents.show', $incident)->with('success', 'Incident enregistré avec succès.');
    }

    public function show(Incident $incident): View
    {
        $incident->load(['reseauEau.zone', 'interventions']);

        return view('back.incidents.show', compact('incident'));
    }

    public function edit(Incident $incident): View
    {
        $reseaux = ReseauEau::with('zone')->orderBy('type')->get();

        return view('back.incidents.edit', compact('incident', 'reseaux'));
    }

    public function update(IncidentRequest $request, Incident $incident): RedirectResponse
    {
        $incident->update($request->validated());

        return redirect()->route('admin.incidents.show', $incident)->with('success', 'Incident modifié avec succès.');
    }

    public function destroy(Incident $incident): RedirectResponse
    {
        $incident->delete();

        return redirect()->route('admin.incidents.index')->with('success', 'Incident et ses interventions associées supprimés.');
    }
}
