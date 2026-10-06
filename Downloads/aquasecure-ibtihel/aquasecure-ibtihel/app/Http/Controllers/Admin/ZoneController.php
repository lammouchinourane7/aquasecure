<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ZoneRequest;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ZoneController extends Controller
{
    public function index(Request $request): View
    {
        $zones = Zone::query()
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q2) => $q2
                ->where('nom', 'like', '%'.$request->string('q').'%')
                ->orWhere('commune', 'like', '%'.$request->string('q').'%')
            ))
            ->withCount('reseauEaus')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('back.zones.index', compact('zones'));
    }

    public function create(): View
    {
        return view('back.zones.create');
    }

    public function store(ZoneRequest $request): RedirectResponse
    {
        Zone::create($request->validated());

        return redirect()->route('admin.zones.index')->with('success', 'Zone créée avec succès.');
    }

    public function edit(Zone $zone): View
    {
        return view('back.zones.edit', compact('zone'));
    }

    public function update(ZoneRequest $request, Zone $zone): RedirectResponse
    {
        $zone->update($request->validated());

        return redirect()->route('admin.zones.index')->with('success', 'Zone modifiée avec succès.');
    }

    public function destroy(Zone $zone): RedirectResponse
    {
        $zone->delete();

        return redirect()->route('admin.zones.index')->with('success', 'Zone supprimée avec succès.');
    }
}
