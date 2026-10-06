@extends('layouts.back')

@section('title', 'Nouveau projet de rénovation')

@section('content')
    <a href="{{ route('admin.projets.index') }}" class="text-sm text-muted hover:text-ink">&larr; Projets de rénovation</a>

    <h1 class="font-display text-xl font-bold text-ink mt-3 mb-6">Ajouter un projet de rénovation</h1>

    <div class="bg-white border border-border rounded-xl p-6 sm:p-8 max-w-3xl">
        <form method="POST" action="{{ route('admin.projets.store') }}" novalidate>
            @csrf

            @include('back.projets._form')

            <div class="flex items-center gap-3 pt-7">
                <x-button type="submit" variant="primary">Créer le projet</x-button>
                <a href="{{ route('admin.projets.index') }}" class="text-sm font-medium text-muted hover:text-ink">Annuler</a>
            </div>
        </form>
    </div>
@endsection
