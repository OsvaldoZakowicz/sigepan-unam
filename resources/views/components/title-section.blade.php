@props([
  'title' => 'titulo'
])

<section class="flex items-center justify-between px-1 py-1 bg-neutral-200">
  <span class="text-sm capitalize">{{ $title }}</span>
  <div class="flex items-center justify-end">
    {{ $slot }}
  </div>
</section>
