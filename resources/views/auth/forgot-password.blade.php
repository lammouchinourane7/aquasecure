<x-guest-layout>
    <h2 class="font-display text-2xl font-bold text-ink">Mot de passe oublié</h2>
    <p class="text-sm text-ink/60 mt-1 mb-6">
        {{ __('Indiquez-nous votre adresse e-mail et nous vous enverrons un lien de réinitialisation.') }}
    </p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <x-primary-button class="w-full">
            {{ __('Envoyer le lien de réinitialisation') }}
        </x-primary-button>

        <p class="text-center text-sm text-ink/60">
            <a href="{{ route('login') }}" class="font-medium text-primary hover:underline">Retour à la connexion</a>
        </p>
    </form>
</x-guest-layout>
