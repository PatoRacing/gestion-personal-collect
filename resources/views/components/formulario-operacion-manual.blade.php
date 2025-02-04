@props(['productos', 'paso'])
@php
    $hoy = date('Y-m-d');
@endphp
<div class="mt-1">
    @if($paso == 1)
        <h4 class="font-bold text-center py-1.5 bg-gray-200">
            Paso 1: Información del Deudor
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-1 p-2 border mt-1">
            <!--Nombre-->
            <div>
                <x-input-label class="ml-1 text-sm" for="deudor" :value="__('Deudor/a:')" />
                <x-text-input
                    id="deudor"
                    placeholder="Nombre y Apellido"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="deudor"
                    wire:model="deudor"
                    />
                <x-input-error :messages="$errors->get('deudor')" class="mt-2" />
            </div>
            <!--Tipo Doc-->
            <div>
                <x-input-label class="ml-1 text-sm" for="tipo_doc" :value="__('Tipo Doc:')" />
                <x-text-input
                    id="tipo_doc"
                    placeholder="Tipo Documento"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="tipo_doc"
                    wire:model="tipo_doc"
                    />
                <x-input-error :messages="$errors->get('tipo_doc')" class="mt-2" />
            </div>
            <!--Documento-->
            <div>
                <x-input-label class="ml-1 text-sm" for="nro_doc" :value="__('Documento:')" />
                <x-text-input
                    id="nro_doc"
                    placeholder="Solo números"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="nro_doc"
                    wire:model="nro_doc"
                    />
                <x-input-error :messages="$errors->get('nro_doc')" class="mt-2" />
            </div>
            <!--Domicilio-->
            <div class="mt-1">
                <x-input-label class="ml-1 text-sm" for="domicilio" :value="__('Domicilio:')" />
                <x-text-input
                    id="domicilio"
                    placeholder="Domicilio"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="domicilio"
                    wire:model="domicilio"
                    />
                <x-input-error :messages="$errors->get('domicilio')" class="mt-2" />
            </div>
            <!--Localidad-->
            <div class="mt-1">
                <x-input-label class="ml-1 text-sm" for="localidad" :value="__('Localidad:')" />
                <x-text-input
                    id="localidad"
                    placeholder="Localidad"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="localidad"
                    wire:model="localidad"
                    />
                <x-input-error :messages="$errors->get('localidad')" class="mt-2" />
            </div>
            <!--Cod. Postal-->
            <div class="mt-1">
                <x-input-label class="ml-1 text-sm" for="codigo_postal" :value="__('Cod. Postal:')" />
                <x-text-input
                    id="codigo_postal"
                    placeholder="Cod. Postal"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="codigo_postal"
                    wire:model="codigo_postal"
                    />
                <x-input-error :messages="$errors->get('codigo_postal')" class="mt-2" />
            </div>
        </div>
        <!--botonera-->
        <div class="grid grid-cols-2 gap-1 p-2">
            <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                    wire:click.prevent="modalOperacionManual(2)">
                Cancelar
            </button>
            <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800"
                    wire:click.prevent="modalOperacionManual(3)">
                Siguiente
            </button>
        </div>
    @elseif($paso == 2)
        <h4 class="font-bold text-center py-1.5 bg-gray-200">
            Paso 2: Información de Contacto
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-1 p-2 border mt-1">
            <!--Tipo Contacto-->
            <div>
                <x-input-label class="ml-1 text-sm" for="tipo" :value="__('Tipo de Contacto:')" />
                <select
                    id="tipo"
                    class="block mt-1 w-full rounded-md border-gray-300 text-sm"
                    wire:model="tipo">
                        <option value="">Seleccionar</option>
                        <option value="Celular">Celular</option>
                        <option value="Telefono Fijo">Tel. Fijo</option>
                        <option value="WhatsApp">WhatsApp</option>
                        <option value="Email">Email</option>
                </select>
                <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
            </div>
            <!--Contacto-->
            <div >
                <x-input-label class="ml-1 text-sm" for="contacto" :value="__('Contacto:')" />
                <select
                    id="contacto"
                    class="block mt-1 w-full rounded-md border-gray-300 text-sm"
                    wire:model="contacto">
                        <option value="">Seleccionar</option>
                        <option value="Titular">Titular</option>
                        <option value="Referencia">Referencia</option>
                        <option value="Laboral">Laboral</option>
                        <option value="Familiar">Familiar</option>
                </select>
                <x-input-error :messages="$errors->get('contacto')" class="mt-2" />
            </div>
            <!--Número-->
            <div class="mt-1">
                <x-input-label class="ml-1 text-sm" for="numero" :value="__('Nro. contacto:')" />
                <x-text-input
                    id="numero"
                    placeholder="Nro. Contacto"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="numero"
                    wire:model="numero"
                    />
                <x-input-error :messages="$errors->get('numero')" class="mt-2" />
            </div>
            <!--Cuil-->
            <div class="mt-1">
                <x-input-label class="ml-1 text-sm" for="cuil" :value="__('CUIL:')" />
                <x-text-input
                    id="cuil"
                    placeholder="Solo números"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="cuil"
                    wire:model="cuil"
                    />
                <x-input-error :messages="$errors->get('cuil')" class="mt-2" />
            </div>
        </div>
        <!--botonera-->
        <div class="grid grid-cols-2 gap-1 p-2">
            <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                    wire:click.prevent="modalOperacionManual(4)">
                Anterior
            </button>
            <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800"
                    wire:click.prevent="modalOperacionManual(5)">
                Siguiente
            </button>
        </div>
    @elseif($paso == 3)
        <h4 class="font-bold text-center py-1.5 bg-gray-200">
            Paso 3: Información de la operación
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-1 p-2 border mt-1">
            <!--Productos-->
            <div >
                <x-input-label class="ml-1 text-sm" for="producto_id" :value="__('Producto:')" />
                <select
                    id="producto_id"
                    class="block mt-1 w-full rounded-md border-gray-300 text-sm"
                    wire:model="producto_id">
                        <option value="">Seleccionar</option>
                        @foreach ($productos as $producto)
                            <option value="{{$producto->id}}">{{$producto->nombre}}</option>
                        @endforeach
                </select>
                <x-input-error :messages="$errors->get('producto_id')" class="mt-2" />
            </div>
            <!--Segmento-->
            <div>
                <x-input-label class="ml-1 text-sm" for="segmento" :value="__('Segmento:')" />
                <x-text-input
                    id="segmento"
                    placeholder="Segmento"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="segmento"
                    wire:model="segmento"
                    />
                <x-input-error :messages="$errors->get('segmento')" class="mt-2" />
            </div>
            <!--Operacion-->
            <div>
                <x-input-label class="ml-1 text-sm" for="operacion" :value="__('Nro. operación:')" />
                <x-text-input
                    id="operacion"
                    placeholder="Operación"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="operacion"
                    wire:model="operacion"
                    />
                <x-input-error :messages="$errors->get('operacion')" class="mt-2" />
            </div>
            <!--Sucursal-->
            <div>
                <x-input-label class="ml-1 text-sm" for="sucursal" :value="__('Sucursal:')" />
                <x-text-input
                    id="sucursal"
                    placeholder="Sucursal"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="sucursal"
                    wire:model="sucursal"
                    />
                <x-input-error :messages="$errors->get('sucursal')" class="mt-2" />
            </div>
            <!--Fecha Atraso-->
            <div>
                <x-input-label class="ml-1 text-sm" for="fecha_atraso" :value="__('Fecha Atraso:')" />
                <x-text-input
                    id="fecha_atraso"
                    class="block mt-1 w-full text-sm"
                    type="date"
                    name="fecha_atraso"
                    wire:model="fecha_atraso"
                    max="{{ $hoy }}"
                    />
                <x-input-error :messages="$errors->get('fecha_atraso')" class="mt-2" />
            </div>
            <!--Dias Atraso-->
            <div>
                <x-input-label class="ml-1 text-sm" for="dias_atraso" :value="__('Días Atraso:')" />
                <x-text-input
                    id="dias_atraso"
                    placeholder="Solo números"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="dias_atraso"
                    wire:model="dias_atraso"
                    />
                <x-input-error :messages="$errors->get('dias_atraso')" class="mt-2" />
            </div>
        </div>
        <!--botonera-->
        <div class="grid grid-cols-2 gap-1 p-2">
            <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                    wire:click.prevent="modalOperacionManual(6)">
                Anterior
            </button>
            <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800"
                    wire:click.prevent="modalOperacionManual(7)">
                Siguiente
            </button>
        </div>
    @elseif($paso == 4)
        <h4 class="font-bold text-center py-1.5 bg-gray-200">
            Paso 4: Valores de la operación
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-1 p-2 border mt-1">
            <!--Deuda Capital-->
            <div>
                <x-input-label class="ml-1 text-sm" for="deuda_capital" :value="__('Deuda Capital:')" />
                <x-text-input
                    id="deuda_capital"
                    placeholder="Decímales separados por ,"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="deuda_capital"
                    wire:model="deuda_capital"
                    />
                <x-input-error :messages="$errors->get('deuda_capital')" class="mt-2" />
            </div>
            <!--Deuda Total-->
            <div>
                <x-input-label class="ml-1 text-sm" for="deuda_total" :value="__('Deuda Total:')" />
                <x-text-input
                    id="deuda_total"
                    placeholder="Decímales separados por ,"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="deuda_total"
                    wire:model="deuda_total"
                    />
                <x-input-error :messages="$errors->get('deuda_total')" class="mt-2" />
            </div>
            <!--Monto Castigo-->
            <div>
                <x-input-label class="ml-1 text-sm" for="monto_castigo" :value="__('Monto Castigo:')" />
                <x-text-input
                    id="monto_castigo"
                    placeholder="Decímales separados por ,"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="monto_castigo"
                    wire:model="monto_castigo"
                    />
                <x-input-error :messages="$errors->get('monto_castigo')" class="mt-2" />
            </div>
            <!--Compensatorio-->
            <div>
                <x-input-label class="ml-1 text-sm" for="compensatorio" :value="__('Compensatorio:')" />
                <x-text-input
                    id="compensatorio"
                    placeholder="Decímales separados por ,"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="compensatorio"
                    wire:model="compensatorio"
                    />
                <x-input-error :messages="$errors->get('compensatorio')" class="mt-2" />
            </div>
            <!--Punitivos-->
            <div>
                <x-input-label class="ml-1 text-sm" for="punitivos" :value="__('Punitivos:')" />
                <x-text-input
                    id="punitivos"
                    placeholder="Decímales separados por ,"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="punitivos"
                    wire:model="punitivos"
                    />
                <x-input-error :messages="$errors->get('punitivos')" class="mt-2" />
            </div>
            <!--Fecha Apertura-->
            <div>
                <x-input-label class="ml-1 text-sm" for="fecha_apertura" :value="__('Fecha Apertura:')" />
                <x-text-input
                    id="fecha_apertura"
                    class="block mt-1 w-full text-sm"
                    type="date"
                    name="fecha_apertura"
                    wire:model="fecha_apertura"
                    max="{{ $hoy }}"
                    />
                <x-input-error :messages="$errors->get('fecha_apertura')" class="mt-2" />
            </div>
            <!--Fecha Castigo-->
            <div>
                <x-input-label class="ml-1 text-sm" for="fecha_castigo" :value="__('Fecha Castigo:')" />
                <x-text-input
                    id="fecha_castigo"
                    class="block mt-1 w-full text-sm"
                    type="date"
                    name="fecha_castigo"
                    wire:model="fecha_castigo"
                    max="{{ $hoy }}"
                    />
                <x-input-error :messages="$errors->get('fecha_castigo')" class="mt-2" />
            </div>
            <!--Fecha Ult. Pago-->
            <div>
                <x-input-label class="ml-1 text-sm" for="fecha_ult_pago" :value="__('Ult. Pago:')" />
                <x-text-input
                    id="fecha_ult_pago"
                    class="block mt-1 w-full text-sm"
                    type="date"
                    name="fecha_ult_pago"
                    wire:model="fecha_ult_pago"
                    max="{{ $hoy }}"
                    />
                <x-input-error :messages="$errors->get('fecha_ult_pago')" class="mt-2" />
            </div>
            <!--Estado-->
            <div>
                <x-input-label class="ml-1 text-sm" for="estado" :value="__('Estado:')" />
                <x-text-input
                    id="estado"
                    placeholder="Estado"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="estado"
                    wire:model="estado"
                    />
                <x-input-error :messages="$errors->get('estado')" class="mt-2" />
            </div>
            <!--Ciclo-->
            <div>
                <x-input-label class="ml-1 text-sm" for="ciclo" :value="__('Ciclo:')" />
                <x-text-input
                    id="ciclo"
                    placeholder="Ciclo"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="ciclo"
                    wire:model="ciclo"
                    />
                <x-input-error :messages="$errors->get('ciclo')" class="mt-2" />
            </div>
            <!--Sub Producto-->
            <div>
                <x-input-label class="ml-1 text-sm" for="sub_producto" :value="__('Subproducto:')" />
                <x-text-input
                    id="sub_producto"
                    placeholder="Subproducto"
                    class="block mt-1 w-full text-sm"
                    type="text"
                    name="sub_producto"
                    wire:model="sub_producto"
                    />
                <x-input-error :messages="$errors->get('sub_producto')" class="mt-2" />
            </div>
            <!--Fecha Asignación-->
            <div>
                <x-input-label class="ml-1 text-sm" for="fecha_asignacion" :value="__('Asignación:')" />
                <x-text-input
                    id="fecha_asignacion"
                    class="block mt-1 w-full text-sm"
                    type="date"
                    name="fecha_asignacion"
                    wire:model="fecha_asignacion"
                    max="{{ $hoy }}"
                    />
                <x-input-error :messages="$errors->get('fecha_asignacion')" class="mt-2" />
            </div>
        </div>
        <!--botonera-->
        <div class="grid grid-cols-2 gap-1 p-2">
            <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                    wire:click.prevent="modalOperacionManual(8)">
                Anterior
            </button>
            <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800"
                    wire:click.prevent="modalOperacionManual(9)">
                Guardar
            </button>
        </div>
    @endif
</div>
    

