@extends('layouts.back')

@section('title', "Modifier l'utilisateur")

@section('content')
    <h1 class="font-display text-xl font-bold text-ink mb-6">Modifier l'utilisateur</h1>

    <div class="bg-white border border-border rounded-xl p-6 sm:p-8 max-w-xl">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="name" value="Nom" />
                <x-text-input id="name" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" type="email" name="email" :value="old('email', $user->email)" required />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="password" value="Mot de passe" />
                <x-text-input id="password" type="password" name="password" />
                <p class="mt-1.5 text-xs text-muted">Laissez ce champ vide pour conserver le mot de passe actuel.</p>
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" />
            </div>

            <div>
                <x-input-label for="role" value="Rôle" />
                <x-select id="role" name="role" required>
                    <option value="citoyen" @selected(old('role', $user->role) === 'citoyen')>Citoyen</option>
                    <option value="gestionnaire" @selected(old('role', $user->role) === 'gestionnaire')>Gestionnaire</option>
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                </x-select>
                <x-input-error :messages="$errors->get('role')" />
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-button type="submit" variant="primary">Enregistrer</x-button>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-muted hover:text-ink">Annuler</a>
            </div>
        </form>
    </div>
@endsection
