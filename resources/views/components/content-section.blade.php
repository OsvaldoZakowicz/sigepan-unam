@props([
  'header',
  'content',
  'footer'
])

{{-- content section --}}
<section {{ $attributes->merge([
  'class' => 'flex flex-col pt-2 px-1 text-sm capitalize bg-white'
]) }}>

  {{-- header --}}
  <section {{ $header->attributes->merge([
    'class' => 'flex items-center justify-between gap-4 p-1 m-1 border rounded-sm bg-neutral-100'
  ]) }}>

    {{ $header }}

  </section>

  {{-- content body --}}
  <section {{ $content->attributes->merge([
    'class' => 'flex mt-2 px-1'
  ]) }}>

    {{ $content }}

  </section>

  {{-- footer --}}
  <section {{ $footer->attributes->merge([
    'class' => 'flex items-center justify-end mt-2 px-1'
  ]) }}>

    {{ $footer }}

  </section>
</section>
