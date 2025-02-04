@section('titulo')
    Usuarios
@endsection

<x-app-layout>
    <!--titulo de la pagina-->
    <h1 class="{{ config('classes.titulo') }}">Usuarios</h1>
    <!--Contenedor principal-->
    <div class="{{ config('classes.contenedorPrincipal') }}">
        <livewire:usuarios.usuarios>
    </div>
</x-app-layout>