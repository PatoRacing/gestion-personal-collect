@section('titulo')
    Acuerdos
@endsection

<x-app-layout>
    <!--titulo de la pagina-->
    <h1 class="{{ config('classes.titulo') }}">Acuerdos</h1>
    <!--Contenedor principal-->
    <div class="{{ config('classes.contenedorPrincipal') }}">
        <livewire:acuerdos.acuerdos />
    </div>
</x-app-layout>