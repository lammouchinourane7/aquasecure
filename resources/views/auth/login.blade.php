<x-guest-layout>
    <h2 class="font-display text-2xl font-bold text-ink">Connexion</h2>
    <p class="text-sm text-ink/60 mt-1 mb-6">Accédez à votre espace AquaSecure.</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Mot de passe')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-ink/25 text-primary focus:ring-primary/30" name="remember">
                <span class="text-sm text-ink/70">{{ __('Se souvenir de moi') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-primary hover:underline" href="{{ route('password.request') }}">
                    {{ __('Mot de passe oublié ?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full">
            {{ __('Se connecter') }}
        </x-primary-button>

        @if (Route::has('register'))
            <p class="text-center text-sm text-ink/60">
                Pas encore de compte ?
                <a href="{{ route('register') }}" class="font-medium text-primary hover:underline">S'inscrire</a>
            </p>
        @endif
    </form>
</x-guest-layout>
