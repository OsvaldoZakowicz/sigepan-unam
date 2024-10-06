@props([
  'bg_color' => 'blue-600',
  'border_color' => 'blue-600',
  'text_color' => 'neutral-100'
])

<a {{ $attributes->merge([
  'class' => 'flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-'.$border_color.' bg-'.$bg_color.' text-center text-'.$text_color.' uppercase text-xs',
]) }}>
  @if ($slot->isEmpty())
    <span>enlace</span>
  @else
    <span>{{ $slot }}</span>
  @endif
</a>
