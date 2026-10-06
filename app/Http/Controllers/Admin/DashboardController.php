<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\Intervention;
use App\Models\ReseauEau;
use App\Models\User;
use App\Models\Zone;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            ['value' => Zone::count(), 'label' => 'Zones', 'color' => 'primary'],
            ['value' => ReseauEau::count(), 'label' => 'Réseaux', 'color' => 'success'],
            ['value' => Incident::count(), 'label' => 'Incidents déclarés', 'color' => 'alert'],
            ['value' => Intervention::where('statut', '!=', 'terminee')->count(), 'label' => 'Interventions en cours / planifiées', 'color' => 'warning'],
        ];

        $derniersReseaux = ReseauEau::with('zone')->latest()->take(5)->get();
        $derniersIncidents = Incident::with(['reseauEau.zone'])->withCount('interventions')->latest('date_signalement')->take(5)->get();

        return view('back.dashboard', compact('stats', 'derniersReseaux', 'derniersIncidents'));
    }
}
