<div class="max-h-[28rem]  overflow-y-auto">
    @foreach ($gestionesRealizadas as $index => $gestionRealizada)
        <div class="px-2 text-sm border border-gray-400 mt-1 {{ $index % 2 == 0 ? 'bg-blue-100' : 'bg-white' }}"> 
            <!--Accion realizada-->
            <div class="mt-1">
                <p class="font-bold">Acción realizada:</p>
                <p>{{$gestionRealizada->accion }}</p>
            </div>
            <!--Telefono-->
            <div class="mt-1">
                <p class="font-bold">Teléfono utilizado:</p> 
                <p>{{$gestionRealizada->telefono->numero }}</p>
            </div>
            <!--Observaciones-->
            <div class="my-1">
                <p class="font-bold">Observaciones:</p> 
                <p>{{$gestionRealizada->observaciones}}</p>
            </div>
        </div>
    @endforeach
</div>
