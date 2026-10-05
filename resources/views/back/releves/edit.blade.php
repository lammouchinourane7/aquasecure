@extends('layouts.back')

@section('title', 'Modifier le relevé')

@section('content')
    <h1 class="font-display text-xl font-bold text-ink mb-6">Modifier le relevé</h1>

    <div class="bg-white border border-border rounded-xl p-6 sm:p-8 max-w-2xl">
        <form method="POST" action="{{ route('admin.releves.update', $releve) }}" novalidate>
            @csrf
            @method('PUT')

            @include('back.releves._form')

            <div class="flex items-center gap-3 pt-7">
                <x-button type="submit" variant="primary">Enregistrer</x-button>
                <a href="{{ route('admin.releves.show', $releve) }}" class="text-sm font-medium text-muted hover:text-ink">Annuler</a>
            </div>
        </form>
    </div>
@endsection
