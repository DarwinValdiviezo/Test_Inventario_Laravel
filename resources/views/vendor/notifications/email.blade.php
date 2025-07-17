<x-mail::message>
<div style="text-align:center; margin-bottom: 20px;">
    <img src="{{ asset('img/logo.png') }}" alt="Logo" style="max-width: 120px;">
</div>
{{-- Saludo --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
# ¡Hola!
@endif

{{-- Líneas de introducción --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Botón de acción --}}
@isset($actionText)
<x-mail::button :url="$actionUrl" color="primary">
{{ $actionText }}
</x-mail::button>
@endisset

{{-- Líneas finales --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Despedida --}}
@if (! empty($salutation))
{{ $salutation }}
@else
Saludos cordiales,<br>
<strong>{{ config('app.name') }}</strong>
@endif

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
@if ($actionText === 'Verificar correo electrónico')
Si tienes problemas para hacer clic en el botón "Verificar correo electrónico", copia y pega la siguiente URL en tu navegador:
@else
Si tienes problemas para hacer clic en el botón "{{ $actionText }}", copia y pega la siguiente URL en tu navegador:
@endif
<span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>