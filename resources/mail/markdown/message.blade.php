@component('filament-postal::layout')
    @slot('icon')
        @component('filament-postal::icon', ['color' => $color])
            {{ $icon }}
        @endcomponent
    @endslot

    @slot('greeting')
        @component('filament-postal::greeting')
            {{ $greeting }}
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('filament-postal::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset
@endcomponent
