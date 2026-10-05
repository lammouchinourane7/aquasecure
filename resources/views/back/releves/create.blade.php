@extends('layouts.back')

@section('title', 'Ajouter un relevé')

@section('content')
    <h1 class="font-display text-xl font-bold text-ink mb-6">Ajouter un relevé</h1>

    <div class="bg-white border border-border rounded-xl p-6 sm:p-8 max-w-2xl">
        <form method="POST" action="{{ route('admin.releves.store') }}" novalidate>
            @csrf

            @include('back.releves._form')

            <div class="flex items-center gap-3 pt-7">
                <x-button type="submit" variant="primary">Enregistrer</x-button>
                <a href="{{ url()->previous() === url()->current() ? route('admin.releves.index') : url()->previous() }}" class="text-sm font-medium text-muted hover:text-ink">Annuler</a>
            </div>
        </form>
    </div>
@endsection
