<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            ['value' => ReseauEau::where('etat', '!=', 'bon')->count(), 'label' => 'Réseaux à surveiller', 'color' => 'warning'],
            ['value' => User::count(), 'label' => 'Utilisateurs', 'color' => 'alert'],
        ];

        $derniersReseaux = ReseauEau::with('zone')->latest()->take(5)->get();

        return view('back.dashboard', compact('stats', 'derniersReseaux'));
    }
}
