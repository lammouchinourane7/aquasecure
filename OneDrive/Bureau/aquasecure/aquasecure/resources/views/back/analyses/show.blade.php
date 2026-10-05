@extends('layouts.back')

@section('title', 'Détail de l\'analyse')

@use('App\Models\AnalyseQualite')

@section('content')
    <a href="{{ route('admin.analyses.index') }}" class="text-sm text-muted hover:text-ink">&larr; Analyses qualité</a>

    <div class="flex flex-wrap items-start justify-between gap-4 mt-3 mb-8">
        <div>
            <h1 class="font-display text-xl font-bold text-ink">Analyse du {{ $analyse->date_prelevement->format('d/m/Y') }}</h1>
            <p class="text-sm text-muted mt-1">
                <a href="{{ route('admin.points.show', $analyse->point_id) }}" class="hover:text-primary">{{ $analyse->point?->code }} · {{ $analyse->point?->nom }}</a>
                · {{ $analyse->laboratoire }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <x-badge :color="$analyse->conforme ? 'success' : 'alert'">{{ $analyse->conforme ? 'Conforme' : 'Non conforme' }}</x-badge>
            <x-button href="{{ route('admin.analyses.edit', $analyse) }}" variant="secondary">Modifier</x-button>
            <form action="{{ route('admin.analyses.destroy', $analyse) }}" method="POST" onsubmit="return confirm('Supprimer cette analyse ?');">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">Supprimer</x-button>
            </form>
        </div>
    </div>

    @unless ($analyse->conforme)
        <div class="rounded-xl border border-alert/30 bg-alert/5 px-5 py-4 mb-6 max-w-3xl">
            <p class="text-sm font-semibold text-alert-strong">Eau non conforme aux normes de potabilité</p>
            <p class="text-sm text-ink/80 mt-1">Paramètre(s) en cause : {{ implode(', ', $analyse->parametresNonConformes()) }}.</p>
        </div>
    @endunless

    <div class="bg-white border border-border rounded-xl overflow-hidden max-w-3xl mb-6">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-muted">
                    <th class="px-6 py-3 font-medium">Paramètre</th>
                    <th class="px-6 py-3 font-medium">Valeur</th>
                    <th class="px-6 py-3 font-medium">Norme</th>
                    <th class="px-6 py-3 font-medium">Analyse précédente</th>
                    <th class="px-6 py-3 font-medium text-right">Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach (AnalyseQualite::NORMES as $param => $norme)
                    @php $ok = AnalyseQualite::respecteNorme($param, $analyse->{$param}); @endphp
                    <tr class="border-b border-border last:border-b-0">
                        <td class="px-6 py-3.5 text-ink">{{ $norme['label'] }}</td>
                        <td class="px-6 py-3.5 font-semibold {{ $ok ? 'text-ink' : 'text-alert-strong' }}">{{ $analyse->{$param} }} {{ $norme['unite'] }}</td>
                        <td class="px-6 py-3.5 text-muted">{{ AnalyseQualite::normeTexte($param) }}</td>
                        <td class="px-6 py-3.5 text-muted">
                            @if ($precedente)
                                {{ $precedente->{$param} }}
                                @php $diff = round($analyse->{$param} - $precedente->{$param}, 2); @endphp
                                @if ($diff != 0)
                                    <span class="text-xs">({{ $diff > 0 ? '+' : '' }}{{ $diff }})</span>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <x-badge :color="$ok ? 'success' : 'alert'">{{ $ok ? 'OK' : 'Hors norme' }}</x-badge>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-white border border-border rounded-xl p-6 max-w-3xl">
        <dl class="grid sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-muted">Point de prélèvement</dt><dd class="font-medium text-ink">{{ $analyse->point?->code }} — {{ $analyse->point?->type_label }}</dd></div>
            <div><dt class="text-muted">Adresse</dt><dd class="font-medium text-ink">{{ $analyse->point?->adresse }}</dd></div>
            <div><dt class="text-muted">Réseau</dt><dd class="font-medium text-ink">{{ $analyse->point?->reseauEau?->type ?? '—' }}</dd></div>
            <div><dt class="text-muted">Zone</dt><dd class="font-medium text-ink">{{ $analyse->point?->reseauEau?->zone?->nom ?? '—' }}</dd></div>
            <div><dt class="text-muted">Laboratoire</dt><dd class="font-medium text-ink">{{ $analyse->laboratoire }}</dd></div>
            <div><dt class="text-muted">Analyse précédente</dt><dd class="font-medium text-ink">{{ $precedente ? $precedente->date_prelevement->format('d/m/Y') : 'Aucune' }}</dd></div>
        </dl>
    </div>
@endsection
