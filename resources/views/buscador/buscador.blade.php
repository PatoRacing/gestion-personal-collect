@section('titulo')
    Buscador
@endsection

<x-app-layout>
    <!--titulo de la pagina-->
    <h1 class="{{ config('classes.titulo') }}">Buscador</h1>
    <!--Contenedor principal-->
    <div class="{{ config('classes.contenedorPrincipal') }}">
        <livewire:buscador.buscador />
    </div>
</x-app-layout>