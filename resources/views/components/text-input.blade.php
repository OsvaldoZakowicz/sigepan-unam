@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300']) !!}>
