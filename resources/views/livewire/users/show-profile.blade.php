<div>
  <div class="w-full p-4">
    <div class="flex flex-col justify-center gap-8 md:justify-start md:flex-row">
      {{-- cuadro perfil, imagen, email, usuario, rol --}}
      <div class="flex flex-col items-center justify-start">
        <div class="inline-flex items-center justify-center w-24 h-24 p-4 bg-blue-200 border rounded-full">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <circle cx="50" cy="35" r="25" fill="none" stroke="black" stroke-width="4" />
            <path d="M25 90 Q50 65 75 90" fill="none" stroke="black" stroke-width="4" />
          </svg>
        </div>
        <div class="text-center">
          <h3 x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
            x-on:profile-updated.window="name = $event.detail.name"
            class="text-base font-semibold leading-7 tracking-tight text-neutral-900"></h3>
          <p x-data="{{ json_encode(['email' => auth()->user()->email]) }}" x-text="email"
            x-on:profile-updated.window="email = $event.detail.email"
            class="text-sm font-semibold leading-6 text-blue-600"></p>
          <span class="lowercase m-2 px-0.5 bg-neutral-100 border border-neutral-200 rounded-sm">
            {{auth()->user()->getRoleNames()->first()}}
          </span>
        </div>
      </div>
      @if ($user->profile)
      {{-- datos personales --}}
      <div class="flex flex-wrap justify-center gap-8 shrink">
        {{-- persona --}}
        <div class="flex flex-col">
          <span class="inline-flex flex-wrap justify-center md:justify-start">
            <span class="font-semibold">apellidos:</span>
            <span class="ml-1 text-neutral-600">{{$user->profile->last_name}}</span>
          </span>
          <span class="inline-flex flex-wrap justify-center md:justify-start">
            <span class="font-semibold">nombres:</span>
            <span class="ml-1 text-neutral-600">{{$user->profile->first_name}}</span>
          </span>
          <span class="inline-flex flex-wrap justify-center md:justify-start">
            <span class="font-semibold">dni:</span>
            <span class="ml-1 text-neutral-600">{{$user->profile->dni}}</span>
          </span>
          <span class="inline-flex flex-wrap justify-center md:justify-start">
            <span class="font-semibold">telefono:</span>
            <span class="ml-1 text-neutral-600">{{$user->profile->phone_number}}</span>
          </span>
          <span class="inline-flex flex-wrap justify-center md:justify-start">
            <span class="font-semibold">fecha de nacimiento:</span>
            <span class="ml-1 text-neutral-600">{{Date::parse($user->profile->birthdate)->format('d-m-Y')}}</span>
          </span>
          <span class="inline-flex flex-wrap justify-center md:justify-start">
            <span class="font-semibold">genero:</span>
            <span class="ml-1 text-neutral-600">{{$user->profile->gender->gender}}</span>
          </span>

        </div>
        {{-- direccion --}}
        <div class="flex flex-col">
          <span class="inline-flex flex-wrap justify-center md:justify-start">
            <span class="font-semibold">calle:</span>
            <span class="ml-1 text-neutral-600">{{$user->profile->address->street}}</span>
          </span>
          <span class="inline-flex flex-wrap justify-center md:justify-start">
            <span class="font-semibold">numero de calle:</span>
            <span class="ml-1 text-neutral-600">{{$user->profile->address->number}}</span>
          </span>
          <span class="inline-flex flex-wrap justify-center md:justify-start">
            <span class="font-semibold">ciudad:</span>
            <span class="ml-1 text-neutral-600">{{$user->profile->address->city}}</span>
          </span>
          <span class="inline-flex flex-wrap justify-center md:justify-start">
            <span class="font-semibold">codigo postal:</span>
            <span class="ml-1 text-neutral-600">{{$user->profile->address->postal_code}}</span>
          </span>
        </div>
      </div>
      @endif
      {{-- mensaje de pedido para completar perfil --}}
      <div class="flex justify-center grow md:justify-start">
        <div class="flex flex-col gap-4">
          @unlessrole('proveedor')
          @if (!$user->profile)
          <span class="text-sm text-red-400 capitalize">¡aún no ha completado su perfil!</span>
          <a href="{{route('profile-complete')}}"
            class="box-border flex items-center justify-center h-6 p-1 text-xs text-center uppercase bg-blue-600 border border-blue-600 border-solid rounded w-fit text-neutral-100">completar
            perfil</a>
          @else
          <a href="{{route('profile-complete')}}"
            class="box-border flex items-center justify-center h-6 p-1 text-xs text-center uppercase border border-solid rounded w-fit border-neutral-200 bg-neutral-100 text-neutral-600">editar
            perfil</a>
          @endif
          @else
          <div class="flex flex-col pb-2 mb-2 border-b border-neutral-200">
            <span>
              <span class="font-semibold">Proveedor:</span>
              <span>{{ $user->supplier->company_name }}, CUIT {{ $user->supplier->company_cuit }}</span>
            </span>
            <span>
              <span class="font-semibold">IVA:</span>
              <span>{{ $user->supplier->iva_condition }}</span>
            </span>
            <span>
              <span class="font-semibold">Direccion:</span>
              <span>{{ $user->supplier->full_address }}</span>
            </span>
            <span>
              <span class="font-semibold">Contacto:</span>
              <span>Tel:&nbsp;{{ $user->supplier->phone_number }},&nbsp;Correo:&nbsp;{{ $user->email }}</span>
            </span>
            <span>
              <span class="font-semibold">Estado actual en el sistema:</span>
              <span>
                @if ($user->supplier->status_is_active)
                <x-text-tag color="emerald">activo</x-text-tag>
                @else
                <x-text-tag color="red">inactivo</x-text-tag>
                @endif
                <span>&nbsp;desde&nbsp;{{ $user->supplier->status_date->format('d-m-Y') }}.</span>
              </span>
          </div>
          <span>
            <span class="font-semibold">¿Problemas o consultas? contáctenos:&nbsp;</span>
            <span>{{ \App\Models\DatoNegocio::obtenerValor('email') }}</span>
          </span>
          @endunlessrole
        </div>
      </div>
    </div>
  </div>
</div>