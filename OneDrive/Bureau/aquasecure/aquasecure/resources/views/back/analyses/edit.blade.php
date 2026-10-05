@extends('layouts.back')

@section('title', 'Modifier l\'analyse')

@section('content')
    <h1 class="font-display text-xl font-bold text-ink mb-6">Modifier l'analyse du {{ $analyse->date_prelevement->format('d/m/Y') }}</h1>

    <div class="bg-white border border-border rounded-xl p-6 sm:p-8 max-w-3xl">
        <form method="POST" action="{{ route('admin.analyses.update', $analyse) }}" novalidate>
            @csrf
            @method('PUT')

            @include('back.analyses._form')

            <div class="flex items-center gap-3 pt-7">
                <x-button type="submit" variant="primary">Enregistrer</x-button>
                <a href="{{ route('admin.analyses.show', $analyse) }}" class="text-sm font-medium text-muted hover:text-ink">Annuler</a>
            </div>
        </form>
    </div>
@endsection
