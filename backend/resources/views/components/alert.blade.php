@php($type = $type ?? 'info')
<div class="alert {{ $type }}">{{ $message ?? '' }}</div>