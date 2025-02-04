@section('titulo')
    Detalle del Acuerdo
@endsection

<x-app-layout>
    <!--titulo de la pagina-->
    <h1 class="{{ config('classes.titulo') }}">Detalle del Acuerdo</h1>
    <!--Contenedor principal-->
    <div class="{{ config('classes.contenedorPrincipal') }}">
        <livewire:acuerdos.perfil-acuerdo :acuerdo="$acuerdo">
    </div>
</x-app-layout>