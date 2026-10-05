<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReleveRequest;
use App\Models\Capteur;
use App\Models\Releve;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReleveController extends Controller
{
    public function index(Request $request): View
    {
        $releves = Releve::query()
            ->when($request->filled('capteur_id'), fn ($q) => $q->where('capteur_id', $request->integer('capteur_id')))
            ->when($request->input('etat') === 'anomalie', fn ($q) => $q->where('hors_seuil', true))
            ->when($request->input('etat') === 'normal', fn ($q) => $q->where('hors_seuil', false))
            ->when($request->filled('du'), fn ($q) => $q->whereDate('date_releve', '>=', $request->date('du')))
            ->when($request->filled('au'), fn ($q) => $q->whereDate('date_releve', '<=', $request->date('au')))
            ->with('capteur.reseauEau')
            ->orderByDesc('date_releve')
            ->paginate(15)
            ->withQueryString();

        $capteurs = Capteur::orderBy('code')->get();

        return view('back.releves.index', compact('releves', 'capteurs'));
    }

    public function create(Request $request): View
    {
        $capteurs = $this->capteursPourFormulaire();
        $releve = new Releve([
            'capteur_id' => $request->integer('capteur_id') ?: null,
            'date_releve' => now(),
        ]);

        return view('back.releves.create', compact('capteurs', 'releve'));
    }

    public function store(ReleveRequest $request): RedirectResponse
    {
        $releve = Releve::create($request->validated());

        return redirect()
            ->route('admin.capteurs.show', $releve->capteur_id)
            ->with('success', $releve->hors_seuil
                ? 'Relevé enregistré — attention : la valeur est hors de la plage normale !'
                : 'Relevé enregistré avec succès.');
    }

    public function show(Releve $releve): View
    {
        $releve->load('capteur.reseauEau.zone');

        return view('back.releves.show', compact('releve'));
    }

    public function edit(Releve $releve): View
    {
        $capteurs = $this->capteursPourFormulaire();

        return view('back.releves.edit', compact('releve', 'capteurs'));
    }

    public function update(ReleveRequest $request, Releve $releve): RedirectResponse
    {
        $releve->update($request->validated());

        return redirect()->route('admin.releves.show', $releve)->with('success', 'Relevé modifié avec succès.');
    }

    public function destroy(Releve $releve): RedirectResponse
    {
        $releve->delete();

        return redirect()->route('admin.releves.index')->with('success', 'Relevé supprimé avec succès.');
    }

    /**
     * Capteurs proposés dans la liste déroulante, regroupés par réseau.
     */
    private function capteursPourFormulaire()
    {
        return Capteur::with('reseauEau')->orderBy('code')->get()
            ->groupBy(fn (Capteur $c) => $c->reseauEau?->type ?? 'Sans réseau');
    }
}
