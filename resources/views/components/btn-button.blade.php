@props([
  'color' => 'emerald',
  'btn_type' => 'submit'
])

<button {{ $attributes->merge([
  'type' => $btn_type,
  'class' => 'flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-'.$color.'-600 bg-'.$color.'-600 text-center text-neutral-100 uppercase text-xs'
]) }}>
  <div class="inline-flex justify-center items-center gap-2">
    {{ $slot }}
  </div>
</button>
