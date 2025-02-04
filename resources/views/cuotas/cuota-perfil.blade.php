@section('titulo')
    Detalle de la Cuota
@endsection

<x-app-layout>
    <!--titulo de la pagina-->
    <h1 class="{{ config('classes.titulo') }}">Gestionar {{$cuota->concepto}}</h1>
    <!--Contenedor principal-->
    <div class="{{ config('classes.contenedorPrincipal') }}">
        <livewire:cuotas.perfil-cuota :cuota="$cuota" />
    </div>
</x-app-layout>