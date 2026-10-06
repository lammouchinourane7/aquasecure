@extends('layouts.back')

@section('title', 'Ajouter une analyse')

@section('content')
    <h1 class="font-display text-xl font-bold text-ink mb-6">Ajouter une analyse qualité</h1>

    <div class="bg-white border border-border rounded-xl p-6 sm:p-8 max-w-3xl">
        <form method="POST" action="{{ route('admin.analyses.store') }}" novalidate>
            @csrf

            @include('back.analyses._form')

            <div class="flex items-center gap-3 pt-7">
                <x-button type="submit" variant="primary">Enregistrer</x-button>
                <a href="{{ route('admin.analyses.index') }}" class="text-sm font-medium text-muted hover:text-ink">Annuler</a>
            </div>
        </form>
    </div>
@endsection
