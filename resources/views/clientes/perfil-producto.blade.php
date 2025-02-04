@section('titulo')
    Perfil Producto
@endsection

<x-app-layout>
    <!--titulo de la pagina-->
    <h1 class="{{ config('classes.titulo') }}">{{$producto->nombre}}</h1>
    <!--Contenedor principal-->
    <div class="{{ config('classes.contenedorPrincipal') }}">
        <livewire:clientes.perfil-producto :producto="$producto"/>
    </div>
</x-app-layout>