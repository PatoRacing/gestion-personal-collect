@section('titulo')
    Perfil Operacion
@endsection

<x-app-layout>
    <!--titulo de la pagina-->
    <h1 class="{{ config('classes.titulo') }}">{{$operacion->cliente->nombre}}- {{$operacion->producto->nombre}} (op: {{$operacion->operacion}})</h1>
    <!--Contenedor principal-->
    <div class="{{ config('classes.contenedorPrincipal') }}">
        <livewire:gestiones.operacion-gestion :operacion="$operacion"/>
    </div>
</x-app-layout>