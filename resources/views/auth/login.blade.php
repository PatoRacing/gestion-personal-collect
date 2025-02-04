@section('titulo')
    Iniciar Sesión
@endsection

@section('contenido')
    <div>
        <a href="/" class="flex justify-center">
            <x-application-logo />
        </a>
    </div>
    <h1 class="text-center font-bold text-xl mt-10">
        Plataforma de Gestión de Cobranzas
    </h1>
@endsection

<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ml-2 text-sm font-bold">{{ __('Recordarme') }}</span>
            </label>
        </div>
        <div class="flex justify-between my-3">
            <x-link-de-ingreso :href="route('olvide.password')">
                Olvidaste el Password?
            </x-link-de-ingreso>
        </div>
        <x-primary-button class="w-full py-2 px-4 text-sm text-white bg-blue-800 hover:bg-blue-900 rounded">
            {{ __('Iniciar Sesión') }}
        </x-primary-button>
    </form>
</x-guest-layout>
