@extends('layouts.back')

@section('title', 'Utilisateurs')

@php
    $roleBadge = ['admin' => 'primary', 'gestionnaire' => 'warning', 'citoyen' => 'success'];
@endphp

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-display text-xl font-bold text-ink">Utilisateurs</h1>
        <x-button href="{{ route('admin.users.create') }}" variant="primary">
            <x-icon.plus class="w-4 h-4" /> Ajouter
        </x-button>
    </div>

    <form method="GET" class="mb-4">
        <div class="relative max-w-sm">
            <x-icon.search class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2" />
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un utilisateur..."
                   class="w-full rounded-lg border border-border bg-white pl-9 pr-4 py-2 text-sm text-ink focus:border-primary focus:ring-4 focus:ring-primary/15 transition">
        </div>
    </form>

    <div class="bg-white border border-border">
        @if ($users->isEmpty())
            <x-empty-state icon="users" title="Aucun utilisateur" description="Commencez par ajouter un utilisateur.">
                <x-slot:action>
                    <x-button href="{{ route('admin.users.create') }}" variant="primary">Ajouter un utilisateur</x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted">
                            <th class="px-6 py-3 font-medium">Nom</th>
                            <th class="px-6 py-3 font-medium">Email</th>
                            <th class="px-6 py-3 font-medium">Rôle</th>
                            <th class="px-6 py-3 font-medium">Créé le</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="border-b border-border last:border-b-0 hover:bg-bg">
                                <td class="px-6 py-3.5 font-medium text-ink">{{ $user->name }}</td>
                                <td class="px-6 py-3.5 text-ink/80">{{ $user->email }}</td>
                                <td class="px-6 py-3.5">
                                    <x-badge :color="$roleBadge[$user->role] ?? 'neutral'">{{ ucfirst($user->role) }}</x-badge>
                                </td>
                                <td class="px-6 py-3.5 text-muted">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="text-muted hover:text-primary" title="Modifier">
                                            <x-icon.pencil class="w-4 h-4" />
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-muted hover:text-alert-strong" title="Supprimer">
                                                <x-icon.trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if ($users->hasPages())
        <div class="mt-4">{{ $users->links() }}</div>
    @endif
@endsection
