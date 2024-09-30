<div>
  <div class="w-full p-4 capitalize">
    <div class="flex flex-col justify-center md:justify-start md:flex-row gap-8">
      {{-- imagen de perfil --}}
      <div class="flex flex-col items-center justify-start">
        {{-- todo: imagen de usuario, o logo con iniciales --}}
        {{-- <img class="h-24 w-24 rounded-full border" src="" alt="perfil"> --}}
        <div class="inline-flex justify-center items-center h-24 w-24 p-4 rounded-full border bg-blue-200">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <circle cx="50" cy="35" r="25" fill="none" stroke="black" stroke-width="4"/>
            <path d="M25 90 Q50 65 75 90" fill="none" stroke="black" stroke-width="4"/>
          </svg>
        </div>
        <div class="capitalize text-center">
          <h3
            x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
            x-text="name"
            x-on:profile-updated.window="name = $event.detail.name"
            class="text-base font-semibold leading-7 tracking-tight text-neutral-900"></h3>
          <p
            x-data="{{ json_encode(['email' => auth()->user()->email]) }}"
            x-text="email"
            x-on:profile-updated.window="email = $event.detail.email"
            class="text-sm font-semibold leading-6 text-blue-600"></p>
          {{-- todo: mostrar rol --}}
          {{-- <p class="text-sm font-semibold leading-6 text-neutral-600"></p> --}}
        </div>
      </div>
      @if ($user->profile)
      {{-- datos personales --}}
      <div class="flex flex-wrap justify-center gap-8 shrink">
        {{-- persona --}}
        <div class="flex flex-col">
          <span class="inline-flex justify-center md:justify-start flex-wrap">
            <span class="font-semibold">apellidos:</span>
            <span class="text-neutral-600 ml-1">{{$user->profile->last_name}}</span>
          </span>
          <span class="inline-flex justify-center md:justify-start flex-wrap">
            <span class="font-semibold">nombres:</span>
            <span class="text-neutral-600 ml-1">{{$user->profile->first_name}}</span>
          </span>
          <span class="inline-flex justify-center md:justify-start flex-wrap">
            <span class="font-semibold">dni:</span>
            <span class="text-neutral-600 ml-1">{{$user->profile->dni}}</span>
          </span>
          <span class="inline-flex justify-center md:justify-start flex-wrap">
            <span class="font-semibold">telefono:</span>
            <span class="text-neutral-600 ml-1">{{$user->profile->phone_number}}</span>
          </span>
          <span class="inline-flex justify-center md:justify-start flex-wrap">
            <span class="font-semibold">fecha de nacimiento:</span>
            <span class="text-neutral-600 ml-1">{{Date::parse($user->profile->birthdate)->format('d-m-Y')}}</span>
          </span>
          <span class="inline-flex justify-center md:justify-start flex-wrap">
            <span class="font-semibold">genero:</span>
            <span class="text-neutral-600 ml-1">{{$user->profile->gender->gender}}</span>
          </span>

        </div>
        {{-- direccion --}}
        <div class="flex flex-col">
          <span class="inline-flex justify-center md:justify-start flex-wrap">
            <span class="font-semibold">calle:</span>
            <span class="text-neutral-600 ml-1">{{$user->profile->address->street}}</span>
          </span>
          <span class="inline-flex justify-center md:justify-start flex-wrap">
            <span class="font-semibold">numero de calle:</span>
            <span class="text-neutral-600 ml-1">{{$user->profile->address->number}}</span>
          </span>
          <span class="inline-flex justify-center md:justify-start flex-wrap">
            <span class="font-semibold">ciudad:</span>
            <span class="text-neutral-600 ml-1">{{$user->profile->address->city}}</span>
          </span>
          <span class="inline-flex justify-center md:justify-start flex-wrap">
            <span class="font-semibold">codigo postal:</span>
            <span class="text-neutral-600 ml-1">{{$user->profile->address->postal_code}}</span>
          </span>
        </div>
      </div>
      @endif
      {{-- acciones --}}
      <div class="flex grow justify-center md:justify-start">
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
