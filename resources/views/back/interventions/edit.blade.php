@extends('layouts.back')

@section('title', 'Modifier l\'intervention #' . $intervention->id)

@section('content')
    <h1 class="font-display text-xl font-bold text-ink mb-6">Modifier l'intervention #{{ $intervention->id }}</h1>

    <div class="bg-white border border-border rounded-xl p-6 sm:p-8 max-w-2xl">
        <form method="POST" action="{{ route('admin.interventions.update', $intervention) }}" novalidate>
            @csrf
            @method('PUT')

            @include('back.interventions._form')

            <div class="flex items-center gap-3 pt-7">
                <x-button type="submit" variant="primary">Mettre à jour</x-button>
                <a href="{{ route('admin.interventions.show', $intervention) }}" class="text-sm font-medium text-muted hover:text-ink">Annuler</a>
            </div>
        </form>
    </div>
@endsection
