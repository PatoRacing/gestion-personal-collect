@section('titulo')
    Olvidaste tu password?
@endsection
@section('contenido')
    <div>
        <a href="/" class="flex justify-center">
            <x-application-logo />
        </a>
    </div>
    <h1 class="text-center font-bold text-xl mt-10">
        Recuperá tu acceso a la Plataforma
    </h1>
@endsection
<x-guest-layout>
    <div class="mb-4 text-center text-blue-800 font-bold text-sm">
        {{ __('Ingresá el email de registro.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex justify-between my-2">
            <x-link-de-ingreso :href="route('/')">
                Volver
            </x-link-de-ingreso>
        </div>
        <x-primary-button class="w-full py-2 px-4 text-sm text-white bg-blue-800 hover:bg-blue-900 rounded">
            {{ __('Enviar instrucciones') }}
        </x-primary-button>
    </form>
</x-guest-layout>
