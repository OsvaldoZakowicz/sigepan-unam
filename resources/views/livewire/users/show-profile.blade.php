<div>
  <div class="w-full p-4 capitalize">
    <div class="flex flex-col justify-center md:justify-start md:flex-row gap-8">
      {{-- imagen de perfil --}}
      <div class="flex flex-col items-center justify-start">
        <img class="h-24 w-24 rounded-full border" src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=1480&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="perfil">
        <div class="capitalize text-center">
          {{-- todo: reflejar cambios en el usuario y correo como en el dropdown del menu --}}
          <h3 class="text-base font-semibold leading-7 tracking-tight text-neutral-900">{{$user->name}}</h3>
          <p class="text-sm font-semibold leading-6 text-blue-600">{{$user->email}}</p>
          {{-- todo: mostrar rol --}}
          {{-- <p class="text-sm font-semibold leading-6 text-neutral-600"></p> --}}
        </div>
      </div>
      @if ($user->profile)
      {{-- datos personales --}}
      <div class="flex flex-wrap gap-8 shrink">
        {{-- persona --}}
        <div class="flex flex-col shrink">
          <span class="inline-flex flex-wrap">
            <span class="font-semibold">apellidos:</span>
            <span class="text-neutral-600">{{$user->profile->last_name}}</span>
          </span>
          <span class="inline-flex flex-wrap">
            <span class="font-semibold">nombres:</span>
            <span class="text-neutral-600">{{$user->profile->first_name}}</span>
          </span>
          <span class="inline-flex flex-wrap">
            <span class="font-semibold">dni:</span>
            <span class="text-neutral-600">{{$user->profile->dni}}</span>
          </span>
          <span class="inline-flex flex-wrap">
            <span class="font-semibold">telefono:</span>
            <span class="text-neutral-600">{{$user->profile->phone_number}}</span>
          </span>
          <span class="inline-flex flex-wrap">
            <span class="font-semibold">fecha de nacimiento:</span>
            <span class="text-neutral-600">{{Date::parse($user->profile->birthdate)->format('d-m-Y')}}</span>
          </span>
          <span class="inline-flex flex-wrap">
            <span class="font-semibold">genero:</span>
            <span class="text-neutral-600">{{$user->profile->gender->gender}}</span>
          </span>

        </div>
        {{-- direccion --}}
        <div class="flex flex-col shrink">
          <span class="inline-flex flex-wrap">
            <span class="font-semibold">calle:</span>
            <span class="text-neutral-600">{{$user->profile->address->street}}</span>
          </span>
          <span class="inline-flex flex-wrap">
            <span class="font-semibold">numero de calle:</span>
            <span class="text-neutral-600">{{$user->profile->address->number}}</span>
          </span>
          <span class="inline-flex flex-wrap">
            <span class="font-semibold">ciudad:</span>
            <span class="text-neutral-600">{{$user->profile->address->city}}</span>
          </span>
          <span class="inline-flex flex-wrap">
            <span class="font-semibold">codigo postal:</span>
            <span class="text-neutral-600">{{$user->profile->address->postal_code}}</span>
          </span>
        </div>
      </div>
      @endif
      {{-- acciones --}}
      <div class="flex grow">
        <div class="flex flex-col gap-4">
          @if (!$user->profile)
            <span class="capitalize text-sm text-red-600">¡aún no ha completado su perfil!</span>
            <a href="{{route('profile-complete')}}" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-blue-600 bg-blue-600 text-center text-neutral-100 uppercase text-xs">completar perfil</a>
          @else
            <a href="{{route('profile-complete')}}" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-neutral-200 bg-neutral-100 text-center text-neutral-600 uppercase text-xs">editar perfil</a>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
