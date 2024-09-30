@props(['value'])

<label {{ $attributes->merge(['class' => 'font-medium capitalize text-sm text-neutral-700']) }}>
    {{ $value ?? $slot }}
</label>
