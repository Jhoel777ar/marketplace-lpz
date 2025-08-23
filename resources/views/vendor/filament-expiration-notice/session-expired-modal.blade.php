<x-filament::modal
    id="session-expired"
    icon="heroicon-o-exclamation-triangle"
    icon-color="danger"
    alignment="center"
    width="sm"
    :close-by-clicking-away="false"
    :close-by-escaping="false"
    :close-button="false"
    footer-actions-alignment="center"
    display-classes="flex"
>
    <x-slot name="heading">
        {{ __('filament-expiration-notice::expiration-notice.heading') }}
    </x-slot>

    <x-slot name="description">
        {{ __('filament-expiration-notice::expiration-notice.description') }}
    </x-slot>

    <x-slot name="footerActions">
        <x-filament::button
            tag="button"
            onclick="window.location.reload()"
            color="primary"
            class="w-full"
        >
            {{ __('filament-expiration-notice::expiration-notice.button') }}
        </x-filament::button>
    </x-slot>
</x-filament::modal>
