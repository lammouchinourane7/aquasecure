<?php

namespace App\Http\Controllers;

use App\Models\ReseauEau;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the citizen-facing home page — gestionnaires/admins are sent
     * straight to the back office instead, they never see the front office.
     */
    public function index(): View|RedirectResponse
    {
        if (in_array(auth()->user()->role, ['gestionnaire', 'admin'], true)) {
            return redirect()->route('admin.dashboard');
        }

        $reseauxCount = ReseauEau::count();

        return view('front.home', compact('reseauxCount'));
    }
}
