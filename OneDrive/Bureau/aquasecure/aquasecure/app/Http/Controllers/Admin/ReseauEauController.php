<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReseauEauRequest;
use App\Models\ReseauEau;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReseauEauController extends Controller
{
    public function index(Request $request): View
    {
        $reseaux = ReseauEau::query()
            ->when($request->filled('q'), fn ($q) => $q->where('type', 'like', '%'.$request->string('q').'%'))
            ->with('zone')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('back.reseaux.index', compact('reseaux'));
    }

    public function create(): View
    {
        $zones = Zone::orderBy('nom')->get();

        return view('back.reseaux.create', compact('zones'));
    }

    public function store(ReseauEauRequest $request): RedirectResponse
    {
        ReseauEau::create($request->validated());

        return redirect()->route('admin.reseaux.index')->with('success', 'Réseau créé avec succès.');
    }

    public function show(ReseauEau $reseau): View
    {
        $reseau->load('zone');

        return view('back.reseaux.show', ['reseau' => $reseau]);
    }

    public function edit(ReseauEau $reseau): View
    {
        $zones = Zone::orderBy('nom')->get();

        return view('back.reseaux.edit', ['reseau' => $reseau, 'zones' => $zones]);
    }

    public function update(ReseauEauRequest $request, ReseauEau $reseau): RedirectResponse
    {
        $reseau->update($request->validated());

        return redirect()->route('admin.reseaux.index')->with('success', 'Réseau modifié avec succès.');
    }

    public function destroy(ReseauEau $reseau): RedirectResponse
    {
        $reseau->delete();

        return redirect()->route('admin.reseaux.index')->with('success', 'Réseau supprimé avec succès.');
    }
}
