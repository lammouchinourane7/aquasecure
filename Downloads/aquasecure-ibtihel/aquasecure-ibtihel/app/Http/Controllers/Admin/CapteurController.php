<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CapteurRequest;
use App\Models\Capteur;
use App\Models\ReseauEau;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CapteurController extends Controller
{
    public function index(Request $request): View
    {
        $capteurs = Capteur::query()
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q2) => $q2
                ->where('code', 'like', '%'.$request->string('q').'%')
                ->orWhere('modele', 'like', '%'.$request->string('q').'%')
            ))
            ->when($request->filled('type_mesure'), fn ($q) => $q->where('type_mesure', $request->string('type_mesure')))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->string('statut')))
            ->when($request->filled('reseau_id'), fn ($q) => $q->where('reseau_id', $request->integer('reseau_id')))
            ->with(['reseauEau.zone', 'dernierReleve'])
            ->withCount([
                'releves',
                'releves as anomalies_count' => fn ($q) => $q->where('hors_seuil', true),
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $reseaux = ReseauEau::with('zone')->orderBy('type')->get();

        return view('back.capteurs.index', compact('capteurs', 'reseaux'));
    }

    public function create(Request $request): View
    {
        $reseaux = ReseauEau::with('zone')->orderBy('type')->get();
        $capteur = new Capteur([
            'reseau_id' => $request->integer('reseau_id') ?: null,
            'statut' => 'actif',
        ]);

        return view('back.capteurs.create', compact('reseaux', 'capteur'));
    }

    public function store(CapteurRequest $request): RedirectResponse
    {
        $capteur = Capteur::create($request->validated());

        return redirect()->route('admin.capteurs.show', $capteur)->with('success', 'Capteur '.$capteur->code.' créé avec succès.');
    }

    public function show(Capteur $capteur): View
    {
        $capteur->load('reseauEau.zone');

        $stats = $capteur->releves()
            ->selectRaw('COUNT(*) as total, AVG(valeur) as moyenne, MIN(valeur) as minimum, MAX(valeur) as maximum, SUM(CASE WHEN hors_seuil THEN 1 ELSE 0 END) as anomalies')
            ->first();

        // 30 derniers relevés, remis dans l'ordre chronologique pour le graphique
        $serie = $capteur->releves()->orderByDesc('date_releve')->take(30)->get()->sortBy('date_releve')->values();

        $releves = $capteur->releves()->orderByDesc('date_releve')->paginate(10);

        return view('back.capteurs.show', compact('capteur', 'stats', 'serie', 'releves'));
    }

    public function edit(Capteur $capteur): View
    {
        $reseaux = ReseauEau::with('zone')->orderBy('type')->get();

        return view('back.capteurs.edit', compact('capteur', 'reseaux'));
    }

    public function update(CapteurRequest $request, Capteur $capteur): RedirectResponse
    {
        $capteur->update($request->validated());

        // Si les seuils changent, on recalcule l'état (normal / hors seuil) de tous les relevés
        if ($capteur->wasChanged(['seuil_min', 'seuil_max'])) {
            $capteur->releves()->get()->each->save();
        }

        return redirect()->route('admin.capteurs.show', $capteur)->with('success', 'Capteur modifié avec succès.');
    }

    public function destroy(Capteur $capteur): RedirectResponse
    {
        $code = $capteur->code;
        $capteur->delete(); // les relevés associés sont supprimés en cascade (clé étrangère)

        return redirect()->route('admin.capteurs.index')->with('success', 'Capteur '.$code.' et ses relevés supprimés.');
    }
}
