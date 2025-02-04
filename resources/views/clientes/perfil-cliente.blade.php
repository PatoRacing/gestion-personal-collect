@section('titulo')
    Perfil Cliente
@endsection

<x-app-layout>
    <!--titulo de la pagina-->
    <h1 class="{{ config('classes.titulo') }}">{{$cliente->nombre}}</h1>
    <!--Contenedor principal-->
    <div class="{{ config('classes.contenedorPrincipal') }}">
        <livewire:clientes.perfil-cliente :cliente="$cliente"/>
    </div>
</x-app-layout>