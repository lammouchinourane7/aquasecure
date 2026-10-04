@extends('layouts.front')

@section('title', 'Accueil')

@section('content')
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Bienvenue, {{ Auth::user()->name }}</h1>
    <p class="text-gray-600">Consultez l'état du réseau d'eau de votre commune ou signalez un incident.</p>
@endsection
