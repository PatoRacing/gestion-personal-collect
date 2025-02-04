@section('titulo')
    Cartera
@endsection

<x-app-layout>
    <!--titulo de la pagina-->
    <h1 class="{{ config('classes.titulo') }}">Cartera</h1>
    <!--Contenedor principal-->
    <div class="{{ config('classes.contenedorPrincipal') }}">
        <livewire:cartera.listado-de-cartera />
    </div>
</x-app-layout>