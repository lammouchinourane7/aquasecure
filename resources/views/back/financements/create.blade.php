@extends('layouts.back')

@section('title', 'Nouveau financement')

@section('content')
    <a href="{{ request('projet_id') ? route('admin.projets.show', request('projet_id')) : route('admin.financements.index') }}" class="text-sm text-muted hover:text-ink">
        &larr; {{ request('projet_id') ? 'Retour au projet' : 'Financements' }}
    </a>

    <h1 class="font-display text-xl font-bold text-ink mt-3 mb-6">Enregistrer une contribution financière</h1>

    <div class="bg-white border border-border rounded-xl p-6 sm:p-8 max-w-3xl">
        <form method="POST" action="{{ route('admin.financements.store') }}" novalidate>
            @csrf

            @if (request('projet_id'))
                <input type="hidden" name="return_to_projet" value="1">
            @endif

            @include('back.financements._form')

            <div class="flex items-center gap-3 pt-7">
                <x-button type="submit" variant="primary">Enregistrer le financement</x-button>
                <a href="{{ request('projet_id') ? route('admin.projets.show', request('projet_id')) : route('admin.financements.index') }}" class="text-sm font-medium text-muted hover:text-ink">
                    Annuler
                </a>
            </div>
        </form>
    </div>
@endsection
