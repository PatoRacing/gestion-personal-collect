<!-- Page Heading -->
<div class="bg-blue-800 py-1">
    <div class="max-w-7xl mx-auto px-2 grid grid-cols-2">
        <h2 class="text-white text-xs">
            {{ auth()->user()->nombre }}
            {{ auth()->user()->apellido }}
        </h2>
        <h2 class="px-4 text-white text-xs text-right">Plataforma de gestión</h2>
    </div>
</div>
<header class="bg-blue-200 shadow p-2">
    <nav class="p-2">
        @php
            if(auth()->user()->rol == 'Administrador')
            {
                $grid = 'grid-cols-8';
            }
            else
            {
                $grid = 'grid-cols-6';
            }
        @endphp
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-2">
            <div class="hidden sm:grid {{$grid}} gap-2 text-xs">
                <a href="{{ route('perfil') }}"
                    class="{{ request()->routeIs('perfil') ? 'bg-blue-800 text-white' : 'border shadow bg-white' }}
                        block text-center px-4 py-2 rounded">
                    {{ __('Perfil') }}
                </a>
                @if(auth()->user()->rol === 'Administrador')
                    <a href="{{ route('usuarios') }}" 
                       class="{{ request()->routeIs('usuarios') ? 'bg-blue-800 text-white' : 'border shadow bg-white' }}
                        block text-center px-4 py-2 rounded">
                        {{ __('Usuarios') }}
                    </a>
                    <a href="{{ route('clientes') }}" 
                       class="{{ request()->routeIs('clientes') ? 'bg-blue-800 text-white' : 'border shadow bg-white' }}
                        block text-center px-4 py-2 rounded">
                        {{ __('Clientes') }}
                    </a>
                @endif
                <a href="{{ route('cartera') }}"
                   class="{{ request()->routeIs('cartera') ? 'bg-blue-800 text-white' : 'border shadow bg-white' }}
                    block text-center px-4 py-2 rounded">
                    {{ __('Cartera') }}
                </a>
                <a href="{{ route('acuerdos') }}" 
                   class="{{ request()->routeIs('acuerdos') ? 'bg-blue-800 text-white' : 'border shadow bg-white' }}
                    block text-center px-4 py-2 rounded">
                    {{ __('Acuerdos') }}
                </a>
                <a href="{{ route('cuotas') }}" 
                   class="{{ request()->routeIs('cuotas') ? 'bg-blue-800 text-white' : 'border shadow bg-white' }}
                    block text-center px-4 py-2 rounded">
                    {{ __('Cuotas') }}
                </a>
                <a href="{{ route('buscador') }}" 
                   class="{{ request()->routeIs('buscador') ? 'bg-blue-800 text-white' : 'border shadow bg-white' }}
                    block text-center px-4 py-2 rounded">
                    {{ __('Buscar') }}
                </a>
                <!-- Opción de Cerrar Sesión -->
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" 
                            class="w-full text-center px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                            onclick="return confirm('Confirmar cerrar sesión');">
                        {{ __('Cerrar') }}
                    </button>
                </form>
            </div>
            <!--hamburguesa-->
            <div x-data="{ open: false }">
                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <h3 class="px-4">Menú de gestión</h3>
                </div>
                <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <x-responsive-nav-link :href="route('perfil')" :active="request()->routeIs('perfil')">
                            {{ __('Perfil') }}
                        </x-responsive-nav-link>
                        <!-- Menú de navegación -->
                        @if(auth()->user()->rol === 'Administrador')
                            <x-responsive-nav-link :href="route('usuarios')" :active="request()->routeIs('usuarios')">
                                {{ __('Usuarios') }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('clientes')" :active="request()->routeIs('clientes')">
                                {{ __('Clientes') }}
                            </x-responsive-nav-link>
                        @endif
                        <x-responsive-nav-link :href="route('cartera')" :active="request()->routeIs('cartera')">
                            {{ __('Cartera') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('acuerdos')" :active="request()->routeIs('acuerdos')">
                            {{ __('Acuerdos') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('cuotas')" :active="request()->routeIs('cuotas')">
                            {{ __('Cuotas') }}
                        </x-responsive-nav-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Salir') }}
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
