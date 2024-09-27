@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium capitalize text-sm text-neutral-700']) }}>
    {{ $value ?? $slot }}
</label>
