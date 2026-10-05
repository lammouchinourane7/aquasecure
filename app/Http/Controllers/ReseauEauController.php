<?php

namespace App\Http\Controllers;

use App\Models\ReseauEau;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReseauEauController extends Controller
{
    /**
     * Display a read-only, citizen-facing listing of water networks.
     */
    public function index(Request $request): View
    {
        $reseaux = ReseauEau::query()
            ->when($request->filled('zone_id'), fn ($q) => $q->where('zone_id', $request->integer('zone_id')))
            ->when($request->filled('etat'), fn ($q) => $q->where('etat', $request->string('etat')))
            ->with('zone')
            ->orderBy('type')
            ->paginate(12)
            ->withQueryString();

        $zones = Zone::orderBy('nom')->get();

        return view('front.reseaux.index', compact('reseaux', 'zones'));
    }
}
