<x-guest-layout>
    <h2 class="font-display text-2xl font-bold text-ink">Vérification de l'e-mail</h2>
    <p class="text-sm text-ink/60 mt-1 mb-6">
        {{ __('Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer ? Si vous n\'avez pas reçu l\'e-mail, nous pouvons vous en envoyer un autre avec plaisir.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 rounded-xl bg-success/10 border border-success/30 px-4 py-3 text-sm font-medium text-success-strong">
            {{ __('Un nouveau lien de vérification a été envoyé à l\'adresse e-mail que vous avez fournie lors de votre inscription.') }}
        </div>
    @endif

    <div class="flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                {{ __('Renvoyer l\'e-mail') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-ink/60 hover:text-ink">
                {{ __('Déconnexion') }}
            </button>
        </form>
    </div>
</x-guest-layout>
