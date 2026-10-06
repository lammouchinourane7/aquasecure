<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PointPrelevementRequest;
use App\Models\AnalyseQualite;
use App\Models\PointPrelevement;
use App\Models\ReseauEau;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PointPrelevementController extends Controller
{
    public function index(Request $request): View
    {
        $points = PointPrelevement::query()
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q2) => $q2
                ->where('code', 'like', '%'.$request->string('q').'%')
                ->orWhere('nom', 'like', '%'.$request->string('q').'%')
                ->orWhere('adresse', 'like', '%'.$request->string('q').'%')
            ))
            ->when($request->filled('type_point'), fn ($q) => $q->where('type_point', $request->string('type_point')))
            ->when($request->filled('reseau_id'), fn ($q) => $q->where('reseau_id', $request->integer('reseau_id')))
            ->when($request->input('actif') === '1', fn ($q) => $q->where('actif', true))
            ->when($request->input('actif') === '0', fn ($q) => $q->where('actif', false))
            ->with(['reseauEau.zone', 'derniereAnalyse'])
            ->withCount([
                'analyses',
                'analyses as non_conformes_count' => fn ($q) => $q->where('conforme', false),
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $reseaux = ReseauEau::orderBy('type')->get();

        return view('back.points.index', compact('points', 'reseaux'));
    }

    public function create(Request $request): View
    {
        $reseaux = ReseauEau::with('zone')->orderBy('type')->get();
        $point = new PointPrelevement([
            'reseau_id' => $request->integer('reseau_id') ?: null,
            'actif' => true,
        ]);

        return view('back.points.create', compact('reseaux', 'point'));
    }

    public function store(PointPrelevementRequest $request): RedirectResponse
    {
        $point = PointPrelevement::create($request->validated());

        return redirect()->route('admin.points.show', $point)->with('success', 'Point '.$point->code.' créé avec succès.');
    }

    public function show(PointPrelevement $point): View
    {
        $point->load('reseauEau.zone');

        $total = $point->analyses()->count();
        $conformes = $point->analyses()->where('conforme', true)->count();

        // Moyenne de chaque paramètre, comparée ensuite à la norme dans la vue
        $moyennes = $total
            ? $point->analyses()->selectRaw(collect(array_keys(AnalyseQualite::NORMES))
                ->map(fn ($p) => "AVG($p) as $p")->implode(', '))->first()
            : null;

        $analyses = $point->analyses()->orderByDesc('date_prelevement')->paginate(10);

        return view('back.points.show', compact('point', 'total', 'conformes', 'moyennes', 'analyses'));
    }

    public function edit(PointPrelevement $point): View
    {
        $reseaux = ReseauEau::with('zone')->orderBy('type')->get();

        return view('back.points.edit', compact('point', 'reseaux'));
    }

    public function update(PointPrelevementRequest $request, PointPrelevement $point): RedirectResponse
    {
        $point->update($request->validated());

        return redirect()->route('admin.points.show', $point)->with('success', 'Point de prélèvement modifié avec succès.');
    }

    public function destroy(PointPrelevement $point): RedirectResponse
    {
        $code = $point->code;
        $point->delete(); // les analyses associées sont supprimées en cascade (clé étrangère)

        return redirect()->route('admin.points.index')->with('success', 'Point '.$code.' et ses analyses supprimés.');
    }
}
