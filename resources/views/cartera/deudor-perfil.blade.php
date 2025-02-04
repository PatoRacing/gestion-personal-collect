@section('titulo')
    Perfil del Deudor
@endsection

<x-app-layout>
    <!--titulo de la pagina-->
    <h1 class="{{ config('classes.titulo') }}">
        @if(!$deudor->nombre)
            Sin datos
        @else
            {{$deudor->nombre}}
        @endif
        </h1>
    <!--Contenedor principal-->
    <div class="{{ config('classes.contenedorPrincipal') }}">
        <livewire:cartera.perfil-deudor :deudor="$deudor">
    </div>
</x-app-layout>