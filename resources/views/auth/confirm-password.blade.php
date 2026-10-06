<x-guest-layout>
    <h2 class="font-display text-2xl font-bold text-ink">Confirmer le mot de passe</h2>
    <p class="text-sm text-ink/60 mt-1 mb-6">
        {{ __('Ceci est une zone sécurisée de l\'application. Merci de confirmer votre mot de passe avant de continuer.') }}
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Mot de passe')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <x-primary-button class="w-full">
            {{ __('Confirmer') }}
        </x-primary-button>
    </form>
</x-guest-layout>
